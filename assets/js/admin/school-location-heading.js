(function() {
    function escText(s) {
        return String(s).replace(/[&<>"']/g, function(ch) {
            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[ch];
        });
    }

    tinymce.PluginManager.add('school_location_heading', function(editor) {
        editor.addButton('school_location_heading', {
            text: '横線+下線見出し',
            tooltip: '横線+下線見出し',
            onclick: function() {
                var text = editor.selection.getContent({ format: 'text' });
                if (!text || !text.trim()) {
                    text = 'LOCATION';
                }
                editor.insertContent('<h2 class="c-school-heading">' + escText(text.trim()) + '</h2>');
            }
        });
    });
})();
