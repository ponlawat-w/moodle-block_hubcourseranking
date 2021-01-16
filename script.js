if (!BLOCK_HUBCOURSERANKING) {
  var BLOCK_HUBCOURSERANKING = () => {
    require(['jquery'], $ => {

      $(document).ready(() => {

        const getTableHead = config => {
          const $thead = $('<thead>');
          const $tr = $('<tr>');

          $tr.append($('<td>').html('#'));
          $tr.append($('<td>').html(M.str.block_hubcourseranking.coursename));
          if (!config.ismanual) {
            if (config.by === 'recent') {
              $tr.append($('<td>').html(M.str.block_hubcourseranking.uploadeddate));
            } else if (config.by === 'download') {
              $tr.append($('<td>').html(M.str.block_hubcourseranking.downloads));
            } else if (config.by === 'reviews') {
              $tr.append($('<td>').html(M.str.block_hubcourseranking.reviews));
            } else if (config.by === 'rated') {
              $tr.append($('<td>').html(M.str.block_hubcourseranking.ratings));
            }
          }

          $thead.html($tr);
          return $thead;
        };

        const makeUrl = courseid => M.cfg.wwwroot + '/course/view.php?id=' + courseid;

        const makeLink = (text, courseid) => $('<a>').html(text)
          .attr('href', makeUrl(courseid))

        const getTableRow = (i, config, record) => {
          const $tr = $('<tr>')
            .append($('<td>').html(makeLink((i + 1).toString(), record.courseid)))
            .append($('<td>').html(makeLink(record.fullname, record.courseid)));
          if (!config.ismanual) {
            if (config.by === 'recent') {
              $tr.append($('<td>').html(makeLink(record.timecreatedstr, record.courseid)));
            } else if (config.by === 'download') {
              $tr.append($('<td>').html(makeLink(record.downloads, record.courseid)));
            } else if (config.by === 'reviews') {
              $tr.append($('<td>').html(makeLink(record.reviews, record.courseid)));
            } else if (config.by === 'rated') {
              $tr.append($('<td>').html(makeLink(record.rated, record.courseid)));
            }
          }
          $tr.css('cursor', 'pointer');
          $tr.click(() => { window.location = makeUrl(record.courseid); });

          return $tr;
        };
          
        const renderTable = ($body, response, loadDataFunc) => {
          const records = response.records;
          if (!records || !records.length) {
            $body.html('');
            return;
          }
  
          const $table = $('<table>');

          const $thead = getTableHead(response.config);

          const $tbody = $('<tbody>');
          $.each(records, (i, record) => {
            const $row = getTableRow(i, response.config, record);
            $tbody.append($row);
          });
  
          $table.html($thead)
            .append($tbody)
            .addClass('table table-hover table-striped');
          
          const $div = $('<div>').html($table);

          if (loadDataFunc) {
            const $btn = $('<button>')
              .html(M.str.block_hubcourseranking.loadmore)
              .addClass('btn btn-secondary w-100')
              .click(e => {
                const $btn = $(e.target);
                $btn.html(M.str.block_hubcourseranking.loading);
                $btn.prop('disabled', true);
                loadDataFunc(true);
              });
            $div.append($btn);
          }
  
          $body.html($div);
        };

        const $bodys = $('.block-hubcourseranking-body');
        $.each($bodys, (_, body) => {
          const $body = $(body);
          const id = $body.attr('data-id');

          const loadData = async(full = false) => {
            const url = `${M.cfg.wwwroot}/blocks/hubcourseranking/api.php?id=${id}${full ? '&full=1' : ''}`;

            const response = await $.get(url);
            renderTable($body, response, full ? null : loadData);
          };

          const init = () => {
            loadData();
          };

          init();
        });
      });
    });
  };

  BLOCK_HUBCOURSERANKING();
}
