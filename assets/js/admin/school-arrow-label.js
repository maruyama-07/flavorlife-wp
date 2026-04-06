(function () {
    tinymce.PluginManager.add('school_arrow_label', function (editor) {
        editor.addButton('school_arrow_label', {
            text: '矢印ラベル',
            tooltip: '右向き矢印形のラベル（背景 #F5F3EF）。挿入後、文言を編集してください。',
            onclick: function () {
                editor.insertContent(
                    '<div class="c-school-arrow-label">' +
                        '<span class="c-school-arrow-label__inner">アクセス・問い合わせ先</span>' +
                        '</div>'
                );
            },
        });
    });
})();
