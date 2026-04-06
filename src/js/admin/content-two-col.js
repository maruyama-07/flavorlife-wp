(function () {
    tinymce.PluginManager.add('content_two_col', function (editor) {
        /**
         * 2カラム列内のキー操作:
         * - Enter … TinyMCE 既定のまま（段落の分割・新規 <p>）。列を増やさないよう DOM は触らない。
         * - Shift+Enter … 列内のみ <br> を挿入（他エディタの「改行」と同様）。
         * リスト・表セル内はどちらも既定動作。
         */
        editor.on('keydown', function (e) {
            if (e.keyCode !== 13 && e.which !== 13) {
                return;
            }
            if (e.ctrlKey || e.metaKey || e.altKey) {
                return;
            }

            if (!e.shiftKey) {
                return;
            }

            var dom = editor.dom;
            var node = editor.selection.getNode();

            if (dom.getParent(node, 'li')) {
                return;
            }
            if (dom.getParent(node, 'td,th')) {
                return;
            }

            var col = dom.getParent(node, '.c-content-two-col__col');
            if (!col) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            editor.undoManager.transact(function () {
                editor.focus();
                var rng = editor.selection.getRng();
                rng.deleteContents();
                var br = dom.create('br');
                rng.insertNode(br);
                rng.setStartAfter(br);
                rng.setEndAfter(br);
                rng.collapse(true);
                editor.selection.setRng(rng);
            });
            editor.nodeChanged();

            return false;
        });

        editor.addButton('content_two_col', {
            text: '2カラム',
            tooltip: '左右45%の2カラム（SPは縦並び）。列内の1行改行は Shift+Enter',
            onclick: function () {
                var html =
                    '<div class="c-content-two-col">' +
                    '<div class="c-content-two-col__col">' +
                    '<p><br></p>' +
                    '</div>' +
                    '<div class="c-content-two-col__col">' +
                    '<p><br></p>' +
                    '</div>' +
                    '</div>';
                editor.insertContent(html);
            }
        });
    });
})();
