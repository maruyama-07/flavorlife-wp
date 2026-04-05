(function() {
    function escHtml(s) {
        return String(s).replace(/[&<>"']/g, function(ch) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[ch];
        });
    }

    function escAttr(s) {
        return escHtml(s);
    }

    /** #RGB / #RRGGBB（不正時はデフォルト） */
    function normalizeHexColor(raw) {
        var s = String(raw || '').trim();
        if (!s) {
            return '#699';
        }
        if (!/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/.test(s)) {
            return '#699';
        }
        if (s.length === 4) {
            return (
                '#' +
                s.charAt(1) +
                s.charAt(1) +
                s.charAt(2) +
                s.charAt(2) +
                s.charAt(3) +
                s.charAt(3)
            ).toLowerCase();
        }
        return s.toLowerCase();
    }

    function parseBgFromLink(link) {
        if (!link) {
            return '#699';
        }
        var st = link.getAttribute('style') || '';
        var m = st.match(/background(?:-color)?\s*:\s*([^;]+)/i);
        if (!m) {
            return '#699';
        }
        var v = m[1].trim().split(/\s+/)[0];
        if (/^#/.test(v)) {
            return normalizeHexColor(v);
        }
        return '#699';
    }

    function buildCtaBackgroundStyle(hex) {
        return 'background-color: ' + normalizeHexColor(hex) + ';';
    }

    function findSchoolCtaLink(editor, node) {
        return editor.dom.getParent(node, 'a.l-header-school__cta', editor.getBody());
    }

    function openSchoolCtaDialog(editor, editLink) {
        var defaultHref =
            typeof window.schoolTinymceCtaHref === 'string' && window.schoolTinymceCtaHref
                ? window.schoolTinymceCtaHref
                : '/school/contact/';
        var isEdit = !!editLink;

        var hrefVal = isEdit ? editor.dom.getAttrib(editLink, 'href') || defaultHref : defaultHref;
        var labelVal;
        if (isEdit) {
            labelVal = editLink.textContent || '';
        } else {
            labelVal = editor.selection.getContent({ format: 'text' });
            if (!labelVal || !labelVal.trim()) {
                labelVal = 'お申し込みはこちら';
            } else {
                labelVal = labelVal.trim();
            }
        }
        var newTabVal = isEdit ? editor.dom.getAttrib(editLink, 'target') === '_blank' : true;
        var bgVal = isEdit ? parseBgFromLink(editLink) : '#699';
        var knownBgs = ['#699', '#003f2f', '#042c1b', '#998067', '#5c5c5c'];
        var listboxBg = '#699';
        var customBgInitial = '';
        if (knownBgs.indexOf(bgVal) !== -1) {
            listboxBg = bgVal;
        } else {
            listboxBg = 'custom';
            customBgInitial = bgVal;
        }

        var vw =
            window.parent && window.parent.innerWidth ? window.parent.innerWidth : window.innerWidth || 1024;
        var vh =
            window.parent && window.parent.innerHeight ? window.parent.innerHeight : window.innerHeight || 800;
        var modalW = Math.min(480, Math.max(300, vw - 40));
        var modalH = Math.min(520, Math.max(400, Math.floor(vh * 0.85)));

        editor.windowManager.open({
            title: isEdit ? 'ボタンを編集' : 'ボタンを挿入',
            classes: 'mce-school-cta-modal',
            width: modalW,
            height: modalH,
            body: [
                {
                    type: 'textbox',
                    name: 'href',
                    label: 'リンクURL',
                    value: hrefVal,
                    placeholder: 'https:// または / から始まるパス'
                },
                {
                    type: 'textbox',
                    name: 'label',
                    label: '表示テキスト',
                    value: labelVal
                },
                {
                    type: 'listbox',
                    name: 'bg_preset',
                    label: '背景色プリセット',
                    values: [
                        { text: 'デフォルト（#699）', value: '#699' },
                        { text: 'ヘッダー濃緑（#003f2f）', value: '#003f2f' },
                        { text: 'ダークグリーン（#042c1b）', value: '#042c1b' },
                        { text: '茶・バナー系（#998067）', value: '#998067' },
                        { text: 'グレー（#5c5c5c）', value: '#5c5c5c' },
                        { text: 'カスタム（下欄で # を入力）', value: 'custom' }
                    ],
                    value: listboxBg
                },
                {
                    type: 'textbox',
                    name: 'bg_custom',
                    label: 'カスタム色（#RRGGBB・プリセットが「カスタム」のとき）',
                    value: customBgInitial
                },
                {
                    type: 'checkbox',
                    name: 'new_tab',
                    label: '新しいタブで開く',
                    checked: newTabVal
                }
            ],
            onsubmit: function(e) {
                var hrefFinal = (e.data.href || '').trim() || defaultHref;
                var labelFinal = (e.data.label || '').trim() || 'お申し込みはこちら';
                var preset = (e.data.bg_preset || '#699').toString();
                var bgHex =
                    preset === 'custom'
                        ? normalizeHexColor(e.data.bg_custom || '#699')
                        : normalizeHexColor(preset);
                var styleStr = buildCtaBackgroundStyle(bgHex);
                var newTab = !!e.data.new_tab;

                if (isEdit) {
                    editor.undoManager.transact(function() {
                        editor.dom.setAttrib(editLink, 'href', hrefFinal);
                        editor.dom.setAttrib(editLink, 'style', styleStr);
                        if (newTab) {
                            editor.dom.setAttrib(editLink, 'target', '_blank');
                            editor.dom.setAttrib(editLink, 'rel', 'noopener noreferrer');
                        } else {
                            editor.dom.setAttrib(editLink, 'target', null);
                            editor.dom.setAttrib(editLink, 'rel', null);
                        }
                        editLink.textContent = labelFinal;
                    });
                    editor.nodeChanged();
                    editor.selection.select(editLink);
                } else {
                    var attrs =
                        'class="l-header-school__cta" style="' +
                        escAttr(styleStr) +
                        '" href="' +
                        escAttr(hrefFinal) +
                        '"';
                    if (newTab) {
                        attrs += ' target="_blank" rel="noopener noreferrer"';
                    }
                    editor.insertContent(
                        '<p class="p-school-content-cta-wrap"><a ' +
                            attrs +
                            '>' +
                            escHtml(labelFinal) +
                            '</a></p>'
                    );
                }
            }
        });
    }

    tinymce.PluginManager.add('school_cta_button', function(editor) {
        editor.addButton('school_cta_button', {
            text: 'ボタン',
            tooltip:
                'ピル型CTAを挿入。リンク上で押すと編集（背景色・URL・文言）。色はコードモードでも変更可',
            onclick: function() {
                var node = editor.selection.getNode();
                var link = findSchoolCtaLink(editor, node);
                openSchoolCtaDialog(editor, link || null);
            }
        });
    });
})();
