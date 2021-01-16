require(['jquery'], $ => {
  $(document).ready(() => {
    $manualrankings = $('[data-formtype=manualranking]');
    $.each($manualrankings, (_, instance) => {
      const $instance = $(instance);
      const $input = $instance.find('input');
      if (!$input.length) {
        throw 'input not found';
      }

      let ORDER = $input.val().split(';').filter(x => x);

      const updateOrder = () => {
        $instance.find('.label').remove();
        $instance.find('.bg-primary').removeClass('bg-primary text-white');
        $.each(ORDER, (index, id) => {
          const $row = $instance.find(`.manualranking-row[data-courseid=${id}]`);
          if ($row.length) {
            const $order = $row.find('.manualranking-order');
            $order.html($('<span>')
              .html(index + 1)
              .addClass('label label-success'))
              .css('font-size', '1em');
            $row.addClass('bg-primary text-white');
          }
        });
        $input.val(ORDER.join(';'));
      };

      const toggleOrder = id => {
        const index = ORDER.findIndex(o => parseInt(o) === parseInt(id));
        if (index > -1) {
          ORDER.splice(index, 1);
        } else {
          ORDER.push(id);
        }
        updateOrder();
      };

      $instance.find('.manualranking-row').click(e => {
        const $targetrow = $(e.target).closest('tr');
        if (!$targetrow.length) {
          return;
        }
        toggleOrder($targetrow.attr('data-courseid'));
      });

      $instance.find('.manualranking-deselect').click(() => {
        ORDER = [];
        updateOrder();
      });

      updateOrder();
    });
  });
});
