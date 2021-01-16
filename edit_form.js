require(['jquery'], $ => {
  $(document).ready(() => {

    const inputs = [
      {name: 'config_by', manual: false},
      {name: 'config_duration', manual: false},
      {name: 'config_manual', manual: true}
    ];

    const setInput = (input, display) => {
      const $formgroup = $(`[name=${input.name}]`).closest('.form-group');
      if (display) {
        $formgroup.show();
      } else {
        $formgroup.hide();
      }
    };

    const updateRankingType = () => {
      const manual = parseInt($('[name=config_ismanual]:checked').val()) ? true : false;
      $.each(inputs, (_, input) => setInput(input, manual === input.manual));
    };

    $('[name=config_ismanual]').change(() => { updateRankingType(); });

    updateRankingType();
  });
});
