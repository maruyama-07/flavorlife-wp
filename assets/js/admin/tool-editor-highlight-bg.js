(function() {
    /** テーマ CSS の既定背景（インライン未指定時）と一致させる */
    var DEFAULT_HEX = '#f5f2eb';

    function sanitizeHex(raw) {
        var s = (raw || '').trim();
        if (!s) {
            return '';
        }
        if (s.charAt(0) !== '#') {
            s = '#' + s;
        }
        if (/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(s)) {
            return s.toLowerCase();
        }
        return null;
    }

    function expandShortHex(hex) {
        if (!hex || hex.length !== 4) {
            return hex;
        }
        return (
            '#' +
            hex.charAt(1) +
            hex.charAt(1) +
            hex.charAt(2) +
            hex.charAt(2) +
            hex.charAt(3) +
            hex.charAt(3)
        ).toLowerCase();
    }

    /**
     * forced_root_block が p のとき、div を p 内に insertContent すると HTML が壊れる。
     * div と、誤変換で付いた p.c-editor-highlight-block の両方を探す。
     */
    function findHighlightBlock(editor, node) {
        var el = editor.dom.getParent(node, 'div.c-editor-highlight-block', editor.getBody());
        if (el) {
            return el;
        }
        return editor.dom.getParent(node, 'p.c-editor-highlight-block', editor.getBody());
    }

    function readBgState(div) {
        if (!div) {
            return { preset: 'default', custom: '' };
        }
        var st = div.getAttribute('style') || '';
        var m = st.match(/background(?:-color)?\s*:\s*([^;]+)/i);
        if (!m) {
            return { preset: 'default', custom: '' };
        }
        var v = m[1]
            .trim()
            .split(/\s+/)[0]
            .toLowerCase();
        var hex = sanitizeHex(v);
        if (hex === null) {
            return { preset: 'default', custom: '' };
        }
        if (hex.length === 4) {
            hex = expandShortHex(hex);
        }
        if (hex === DEFAULT_HEX) {
            return { preset: 'default', custom: '' };
        }
        return { preset: 'custom', custom: hex };
    }

    /** 段落 inner をブロック内用に整形（プレーンテキストは p で包む） */
    function normalizeParagraphInner(html) {
        var t = String(html || '').replace(/^\s+|\s+$/g, '');
        if (!t) {
            return '<p>ここにテキストを入力してください</p>';
        }
        if (/^<br\s*\/?>$/i.test(t)) {
            return '<p>ここにテキストを入力してください</p>';
        }
        if (/^<[a-z]/i.test(t)) {
            return t;
        }
        return '<p>' + t + '</p>';
    }

    /** 選択範囲がブロック要素の内容全体と一致するか */
    function isRangeCoveringNodeContents(editor, block) {
        if (!block || editor.selection.isCollapsed()) {
            return false;
        }
        var rng = editor.selection.getRng();
        var doc = editor.getDoc();
        try {
            var ref = doc.createRange();
            ref.selectNodeContents(block);
            return (
                rng.compareBoundaryPoints(Range.START_TO_START, ref) === 0 &&
                rng.compareBoundaryPoints(Range.END_TO_END, ref) === 0
            );
        } catch (e) {
            return false;
        }
    }

    /**
     * insertContent(div) は p 内では無効なネストになり、空の p + 本文が分離する。
     * 段落・見出しブロックは DOM で丸ごと差し替える。
     */
    function insertHighlightBlock(editor, hex) {
        var dom = editor.dom;
        var attr = hex ? ' style="background-color: ' + hex + ';"' : '';

        var block = dom.getParent(editor.selection.getNode(), 'p,h1,h2,h3,h4,h5,h6', editor.getBody());
        var inner;
        var replaceNode = null;

        if (block) {
            var collapsed = editor.selection.isCollapsed();
            if (collapsed) {
                replaceNode = block;
                if (block.nodeName === 'P') {
                    inner = normalizeParagraphInner(block.innerHTML);
                } else {
                    inner = block.outerHTML;
                }
            } else if (isRangeCoveringNodeContents(editor, block)) {
                replaceNode = block;
                inner = editor.selection.getContent({ format: 'html' });
                if (!inner || !String(inner).replace(/^\s+|\s+$/g, '')) {
                    inner = '<p>ここにテキストを入力してください</p>';
                } else if (block.nodeName === 'P' && !/^<[a-z]/i.test(inner.trim())) {
                    inner = normalizeParagraphInner(inner);
                }
            }
        }

        if (replaceNode) {
            editor.undoManager.transact(function() {
                var div = dom.create('div', { class: 'c-editor-highlight-block' });
                if (hex) {
                    div.setAttribute('style', 'background-color: ' + hex + ';');
                }
                dom.setHTML(div, inner);
                dom.replace(div, replaceNode);
                editor.selection.select(div, true);
                editor.selection.collapse(false);
            });
            editor.nodeChanged();
            return;
        }

        var looseInner = editor.selection.getContent({ format: 'html' });
        if (looseInner && String(looseInner).replace(/^\s+|\s+$/g, '')) {
            inner = looseInner;
            if (!/^<[a-z]/i.test(String(inner).trim())) {
                inner = '<p>' + inner + '</p>';
            }
        } else {
            inner = '<p>ここにテキストを入力してください</p>';
        }

        editor.insertContent(
            '<div class="c-editor-highlight-block"' + attr + '>' + inner + '</div>'
        );
        editor.nodeChanged();
    }

    function openModal(editor, existingDiv) {
        var isEdit = !!existingDiv;
        var state = readBgState(existingDiv);

        var vw =
            window.parent && window.parent.innerWidth ? window.parent.innerWidth : window.innerWidth || 1024;
        var modalW = Math.min(440, Math.max(300, vw - 40));

        editor.windowManager.open({
            title: isEdit ? '背景ハイライトを編集' : '背景ハイライトを挿入',
            width: modalW,
            body: [
                {
                    type: 'listbox',
                    name: 'bg_preset',
                    label: '背景色',
                    values: [
                        { text: '既定（薄ベージュ #' + DEFAULT_HEX.replace('#', '') + '）', value: 'default' },
                        { text: 'カスタム（下欄で # を入力）', value: 'custom' }
                    ],
                    value: state.preset
                },
                {
                    type: 'textbox',
                    name: 'bg_custom',
                    label: 'カスタム色（# ＋ 3桁または6桁）',
                    value: state.custom
                },
                {
                    type: 'container',
                    html:
                        '<p style="margin:8px 0 0;font-size:12px;color:#646970;line-height:1.5">' +
                        '上下・左に余白の付いた帯状のブロックです。段落内ではカーソル位置の段落全体がブロック化されます（一部だけ選択した場合は挿入位置に新規ブロック）。' +
                        '</p>'
                }
            ],
            onsubmit: function(e) {
                var preset = (e.data.bg_preset || 'default').toString();
                var hex = '';
                if (preset === 'custom') {
                    var raw = (e.data.bg_custom || '').trim();
                    hex = sanitizeHex(raw);
                    if (raw !== '' && hex === null) {
                        editor.windowManager.alert(
                            '色の形式が正しくありません。#で始まる3桁または6桁の16進数にしてください。'
                        );
                        return;
                    }
                    if (!hex) {
                        editor.windowManager.alert(
                            '「カスタム」を選んだときは、# で始まる色を入力してください。'
                        );
                        return;
                    }
                }

                if (isEdit) {
                    editor.undoManager.transact(function() {
                        editor.dom.setAttrib(existingDiv, 'class', 'c-editor-highlight-block');
                        if (hex) {
                            editor.dom.setAttrib(existingDiv, 'style', 'background-color: ' + hex + ';');
                        } else {
                            editor.dom.setAttrib(existingDiv, 'style', null);
                        }
                    });
                    editor.nodeChanged();
                    editor.selection.select(existingDiv);
                    return;
                }

                insertHighlightBlock(editor, hex);
            }
        });
    }

    tinymce.PluginManager.add('tool_editor_highlight', function(editor) {
        editor.addButton('tool_editor_highlight', {
            text: '背景ハイライト',
            tooltip: '薄い背景色の帯（色はモーダルで変更・ブロック内で押すと編集）',
            onclick: function() {
                var node = editor.selection.getNode();
                var block = findHighlightBlock(editor, node);
                openModal(editor, block || null);
            }
        });
    });
})();
