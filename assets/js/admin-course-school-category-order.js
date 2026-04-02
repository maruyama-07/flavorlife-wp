(function ($) {
  'use strict';

  $(function () {
    var $list = $('#the-list');
    if (!$list.length || typeof $list.sortable !== 'function') {
      return;
    }

    var $rows = $list.children('tr[id^="tag-"]');
    if ($rows.length < 2) {
      return;
    }

    $list.sortable({
      items: '> tr[id^="tag-"]',
      cursor: 'move',
      axis: 'y',
      containment: 'parent',
      tolerance: 'pointer',
      update: function () {
        var ids = [];
        $list.children('tr[id^="tag-"]').each(function () {
          var raw = this.id.replace(/^tag-/, '');
          if (raw) {
            ids.push(parseInt(raw, 10));
          }
        });
        if (!ids.length || !window.courseSchoolCategoryOrder) {
          return;
        }
        $.post(window.courseSchoolCategoryOrder.ajaxUrl, {
          action: 'course_school_save_category_order',
          nonce: window.courseSchoolCategoryOrder.nonce,
          order: ids.join(',')
        });
      }
    });
  });
})(jQuery);
