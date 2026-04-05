(function() {
    function escHtml(s) {
        return String(s).replace(/[&<>"']/g, function(ch) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[ch];
        });
    }

    function escAttr(s) {
        return escHtml(s);
    }

    function clampWidth(n) {
        var x = parseInt(n, 10);
        if (isNaN(x)) {
            return 42;
        }
        return Math.min(90, Math.max(10, x));
    }

    /** 空なら ''（未入力時は __text ブロックごと出さない） */
    function bodyToParagraphs(text) {
        var t = String(text || '').trim();
        if (!t) {
            return '';
        }
        var parts = t.split(/\n\s*\n/);
        var html = '';
        for (var i = 0; i < parts.length; i++) {
            var block = parts[i].trim();
            if (!block) {
                continue;
            }
            var inner = escHtml(block).replace(/\n/g, '<br />');
            html += '<p>' + inner + '</p>';
        }
        return html;
    }

    /** TinyMCE は iframe 内のため、メディアは親の wp.media を使う */
    function getWpMedia() {
        if (typeof wp !== 'undefined' && wp.media) {
            return wp.media;
        }
        if (window.parent && window.parent.wp && window.parent.wp.media) {
            return window.parent.wp.media;
        }
        return null;
    }

    function getViewportWidth() {
        if (window.parent && window.parent.innerWidth) {
            return window.parent.innerWidth;
        }
        return window.innerWidth || 1024;
    }

    function getViewportHeight() {
        if (window.parent && window.parent.innerHeight) {
            return window.parent.innerHeight;
        }
        return window.innerHeight || 800;
    }

    /** TinyMCE ダイアログの textbox に値を反映 */
    function setDialogTextboxValue(dialogWin, name, value) {
        var field = dialogWin.find('#' + name);
        if (!field || (field.length !== undefined && field.length === 0)) {
            field = dialogWin.find('textbox[name=' + name + ']');
        }
        if (!field) {
            return;
        }
        if (typeof field.value === 'function') {
            field.value(value);
            return;
        }
        if (field.length && typeof field[0].value === 'function') {
            field[0].value(value);
        }
    }

    function findCourseIntroRoot(editor, node) {
        return editor.dom.getParent(node, '.c-school-course-intro');
    }

    /**
     * WordPress wpeditimage が認識するクラス（鉛筆→画像の詳細→置き換え用）
     * @param {object|null} attachment wp.media の toJSON() 相当（id / url / width / height）
     * @param {string} existingClass 既存 img の class（align* を維持したいとき）
     */
    function wpImageClassString(attachment, existingClass) {
        var align = 'alignnone';
        if (existingClass) {
            var match = String(existingClass).match(/\balign(left|center|right|none)\b/);
            if (match) {
                align = match[0];
            }
        }
        if (attachment && attachment.id) {
            return align + ' wp-image-' + attachment.id + ' size-full';
        }
        return align;
    }

    /** 挿入用 img タグ（メディア選択時は ID 付きで WP 標準編集と連携） */
    function buildCourseIntroImgHtml(imgUrl, altStr, attachment) {
        var cls = wpImageClassString(attachment, '');
        var w = '';
        var h = '';
        if (attachment && attachment.id) {
            if (attachment.width) {
                w = ' width="' + escAttr(String(attachment.width)) + '"';
            }
            if (attachment.height) {
                h = ' height="' + escAttr(String(attachment.height)) + '"';
            }
        }
        return (
            '<img class="' +
            escAttr(cls) +
            '" src="' +
            escAttr(imgUrl) +
            '" alt="' +
            escAttr(altStr || '') +
            '"' +
            w +
            h +
            ' />'
        );
    }

    /** キャレットが講座2カラム内のとき、メディアで画像だけ差し替え */
    function replaceCourseIntroImage(editor, introEl) {
        var mediaLib = getWpMedia();
        if (!mediaLib) {
            editor.windowManager.alert('メディアライブラリを読み込めません。ページを再読み込みしてください。');
            return;
        }
        var frame = mediaLib({
            title: '画像を差し替え',
            button: { text: 'この画像に差し替え' },
            library: { type: 'image' },
            multiple: false
        });
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            var url = attachment.url;
            if (!url) {
                return;
            }
            var alt = attachment.alt || '';
            var mediaWrap = introEl.querySelector('.c-school-course-intro__media');
            var img = mediaWrap ? mediaWrap.querySelector('img') : introEl.querySelector('img');
            if (!img) {
                editor.windowManager.alert('このブロック内に画像が見つかりません。');
                return;
            }
            editor.undoManager.transact(function() {
                editor.dom.setAttrib(img, 'src', url);
                editor.dom.setAttrib(img, 'alt', alt);
                if (attachment.id) {
                    editor.dom.setAttrib(img, 'class', wpImageClassString(attachment, img.className));
                    if (attachment.width) {
                        editor.dom.setAttrib(img, 'width', String(attachment.width));
                    }
                    if (attachment.height) {
                        editor.dom.setAttrib(img, 'height', String(attachment.height));
                    }
                }
            });
            editor.nodeChanged();
        });
        frame.open();
    }

    function openCourseIntroDialog(editor) {
        var pickedAlt = '';
        /** メディアで選んだ添付（URL が手入力と一致するときだけ wp-image を付与） */
        var pickedAttachment = null;
        var modalWidth = Math.min(520, Math.max(300, getViewportWidth() - 40));
        var vh = getViewportHeight();
        var modalHeight = Math.min(640, Math.max(520, Math.floor(vh * 0.88)));

        var dialogWin = editor.windowManager.open({
            title: '講座紹介2カラムを挿入',
            classes: 'mce-school-course-intro-modal',
            width: modalWidth,
            height: modalHeight,
            body: [
                {
                    type: 'container',
                    html:
                        '<div class="school-course-intro-img-picker" style="margin:0 0 12px;padding:0;max-width:100%;box-sizing:border-box">' +
                        '<p style="margin:0 0 8px">' +
                        '<button type="button" class="button school-course-intro-pick-img">メディアライブラリで画像を選択</button>' +
                        '</p>' +
                        '<p class="school-course-intro-img-status" style="margin:0;font-size:12px;color:#646970;line-height:1.5">未選択（上のボタンで選ぶか、下の画像URLに直接入力）</p>' +
                        '</div>'
                },
                {
                    type: 'textbox',
                    name: 'img_url',
                    label: '画像URL',
                    value: ''
                },
                {
                    type: 'listbox',
                    name: 'media_position',
                    label: '画像の位置',
                    values: [
                        { text: '左（テキストが右）', value: 'left' },
                        { text: '右（テキストが左）', value: 'right' }
                    ],
                    value: 'left'
                },
                {
                    type: 'textbox',
                    name: 'img_width',
                    label: '画像カラムの幅（%・10〜90）',
                    value: '42'
                },
                {
                    type: 'textbox',
                    name: 'title',
                    label: '見出し（30px・左アクセント・未入力なら出力しない）',
                    value: ''
                },
                {
                    type: 'textbox',
                    name: 'body',
                    label: '本文（行間2.2。空行で段落分け）',
                    value: '',
                    multiline: true,
                    minHeight: 120
                },
                {
                    type: 'textbox',
                    name: 'cta_href',
                    label: 'ボタンリンクURL（文言と両方あるときだけボタンを出力）',
                    value: ''
                },
                {
                    type: 'textbox',
                    name: 'cta_label',
                    label: 'ボタン文言',
                    value: ''
                }
            ],
            onsubmit: function(e) {
                var imgUrl = (e.data.img_url || '').trim();
                if (!imgUrl) {
                    editor.windowManager.alert('画像を選択するか、画像URLを入力してください。');
                    return;
                }
                var w = clampWidth(e.data.img_width);
                var titleVal = (e.data.title || '').trim();
                var ctaHref = (e.data.cta_href || '').trim();
                var ctaLabel = (e.data.cta_label || '').trim();
                var bodyHtml = bodyToParagraphs(e.data.body);
                var altStr = pickedAlt ? String(pickedAlt).trim() : '';

                var usePickedAtt =
                    pickedAttachment &&
                    pickedAttachment.url &&
                    String(pickedAttachment.url).trim() === imgUrl;
                var imgHtml = buildCourseIntroImgHtml(
                    imgUrl,
                    altStr,
                    usePickedAtt ? pickedAttachment : null
                );

                var mediaPos = (e.data.media_position || 'left').toString();
                var layoutClass =
                    mediaPos === 'right' ? 'c-school-course-intro c-school-course-intro--media-right' : 'c-school-course-intro';
                var rootStyle = '--c-school-course-intro-img: ' + w + '%;';

                var bodyInner = '';
                if (titleVal) {
                    bodyInner += '<h3 class="c-school-course-intro__title">' + escHtml(titleVal) + '</h3>';
                }
                if (bodyHtml) {
                    bodyInner += '<div class="c-school-course-intro__text">' + bodyHtml + '</div>';
                }
                if (ctaHref && ctaLabel) {
                    bodyInner +=
                        '<div class="c-school-course-intro__cta">' +
                        '<a href="' +
                        escAttr(ctaHref) +
                        '" class="l-header-school__cta">' +
                        escHtml(ctaLabel) +
                        '</a>' +
                        '</div>';
                }

                var html =
                    '<div class="' + layoutClass + '" style="' + escAttr(rootStyle) + '">' +
                    '<div class="c-school-course-intro__media">' +
                    imgHtml +
                    '</div>' +
                    '<div class="c-school-course-intro__body">' +
                    bodyInner +
                    '</div>' +
                    '</div>';

                editor.insertContent(html);
            }
        });

        setTimeout(function() {
            if (!dialogWin || typeof dialogWin.getEl !== 'function') {
                return;
            }
            var root = dialogWin.getEl();
            if (!root) {
                return;
            }
            var btn = root.querySelector('.school-course-intro-pick-img');
            var status = root.querySelector('.school-course-intro-img-status');
            if (!btn) {
                return;
            }
            btn.onclick = function() {
                var mediaLib = getWpMedia();
                if (!mediaLib) {
                    editor.windowManager.alert('メディアライブラリを読み込めません。ページを再読み込みしてください。');
                    return;
                }
                var frame = mediaLib({
                    title: '画像を選択',
                    button: { text: 'この画像を使う' },
                    library: { type: 'image' },
                    multiple: false
                });
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    var url = attachment.url;
                    if (!url) {
                        return;
                    }
                    pickedAttachment = attachment;
                    pickedAlt = attachment.alt || '';
                    setDialogTextboxValue(dialogWin, 'img_url', url);
                    if (status) {
                        var name = attachment.filename || attachment.title || '選択済み';
                        status.textContent = '選択中: ' + name + (pickedAlt ? '（代替テキストあり）' : '');
                    }
                });
                frame.open();
            };
        }, 0);
    }

    tinymce.PluginManager.add('school_course_intro', function(editor) {
        editor.addButton('school_course_intro', {
            text: '講座紹介2カラム',
            tooltip:
                '新規挿入。ブロック内で押すと画像差し替え。メディア経由の画像は鉛筆アイコンからもWP標準の置き換え可',
            onclick: function() {
                var node = editor.selection.getNode();
                var intro = findCourseIntroRoot(editor, node);
                if (intro) {
                    replaceCourseIntroImage(editor, intro);
                } else {
                    openCourseIntroDialog(editor);
                }
            }
        });
    });
})();
