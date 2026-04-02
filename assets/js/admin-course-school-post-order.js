(function ($) {
  'use strict';

  $(function () {
    var $list = $('#the-list');
    if (!$list.length || typeof $list.sortable !== 'function') {
      return;
    }

    var $rows = $list.children('tr[id^="post-"]');
    if ($rows.length < 2) {
      return;
    }

    $list.sortable({
      items: '> tr[id^="post-"]',
      cursor: 'move',
      axis: 'y',
      containment: 'parent',
      tolerance: 'pointer',
      update: function () {
        var ids = [];
        $list.children('tr[id^="post-"]').each(function () {
          var raw = this.id.replace(/^post-/, '');
          if (raw) {
            ids.push(parseInt(raw, 10));
          }
        });
        if (!ids.length || !window.courseSchoolPostOrder) {
          return;
        }
        $.post(window.courseSchoolPostOrder.ajaxUrl, {
          action: 'course_school_save_post_order',
          nonce: window.courseSchoolPostOrder.nonce,
          order: ids.join(',')
        });
      }
    });
  });
})(jQuery);
