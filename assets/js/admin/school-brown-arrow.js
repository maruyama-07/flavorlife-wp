(function() {
    tinymce.PluginManager.add('school_brown_arrow', function(editor) {
        editor.addButton('school_brown_arrow', {
            text: '茶色矢印',
            tooltip: '下向きの茶色三角（PC・スマホで画像が切り替わります）',
            onclick: function() {
                var u = window.schoolTinymceBrownArrow || {};
                var pc = typeof u.pc === 'string' ? u.pc : '';
                var sp = typeof u.sp === 'string' ? u.sp : '';
                if (!pc || !sp) {
                    editor.windowManager.alert(
                        '画像URLが取得できませんでした。画面を再読み込みしてください。'
                    );
                    return;
                }
                function escAttr(s) {
                    return String(s).replace(/&/g, '&amp;').replace(/"/g, '&quot;');
                }
                editor.insertContent(
                    '<div class="c-school-brown-arrow">' +
                        '<img class="c-school-brown-arrow__pc" src="' +
                        escAttr(pc) +
                        '" alt="" width="269" height="60" decoding="async" />' +
                        '<img class="c-school-brown-arrow__sp" src="' +
                        escAttr(sp) +
                        '" alt="" width="118" height="32" decoding="async" />' +
                        '</div>'
                );
            }
        });
    });
})();
