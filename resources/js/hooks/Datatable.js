import { onMounted, onBeforeUnmount } from 'vue';
import $ from 'jquery';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
import 'datatables.net-bs5';

const currentLocale = sessionStorage.getItem("local") ?? 'en';

const languageFiles = {
  ar: 'https://cdn.datatables.net/plug-ins/1.10.21/i18n/Arabic.json',
  nl: 'https://cdn.datatables.net/plug-ins/1.10.21/i18n/Dutch.json',
  en: 'https://cdn.datatables.net/plug-ins/1.10.21/i18n/English.json',
  fr: 'https://cdn.datatables.net/plug-ins/1.10.21/i18n/French.json',
  it: 'https://cdn.datatables.net/plug-ins/1.10.21/i18n/Italian.json',
  pt: 'https://cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese.json',
  es: 'https://cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json',
};

const useDataTable = ({
  tableRef,
  columns,
  data = [],
  url = null,
  actionCallback,
  per_page = 10,
  advanceFilter = undefined,
  dom = '<"row align-items-center"<"col-md-6" l><"col-md-6" f>><"table-responsive my-3" rt><"row align-items-center" <"col-md-6" i><"col-md-6" p>><"clear">'
}) => {
  onMounted(async () => {
    setTimeout(async () => {
      let languageSettings = {};

      const languageUrl = languageFiles[currentLocale] || languageFiles['en'];
      const lat = localStorage.getItem('loction_current_lat');
      const long = localStorage.getItem('loction_current_long');

      let noDataMessage = 'No data available in the table';

      if (lat != '' && long != '') {
        noDataMessage = 'Currently, there are no data available in this zone';
      }

      try {
        const res = await fetch(languageUrl);
        languageSettings = await res.json();
        languageSettings.info = "_START_ to _END_ of _TOTAL_ entries";
        languageSettings.lengthMenu = "Display _MENU_ entries"; // Generic, dynamic part added below
        languageSettings.emptyTable = noDataMessage;
      } catch (err) {
        languageSettings = {
          info: "_START_ to _END_ of _TOTAL_ entries",
          lengthMenu: "Display _MENU_ entries"

        };
      }

      let datatableObj = {
        dom: dom,
        autoWidth: false,
        columns: columns,
        language: languageSettings,

        initComplete: function () {
          const api = this.api();
          const pageInfo = api.page.info();
          const totalEntries = pageInfo.recordsTotal;

          // Inject custom message next to length menu
          const lengthMenuContainer = $(this)
            .closest('.dataTables_wrapper')
            .find('.dataTables_length label');

          lengthMenuContainer.append(`<span class="ms-2 text-muted custom-length-info">Showing 1 to ${pageInfo.end} of ${totalEntries} entries</span>`);

          // Custom styling for tbody
          if (tableRef.value.id === 'helpdesk-datatable') {
            $(tableRef.value).find('tbody').addClass('row row-cols-xl-3 row-cols-lg-3 row-cols-sm-2');
          } else {
            $(tableRef.value).find('tbody').addClass('row row-cols-xl-4 row-cols-lg-3 row-cols-sm-2');
          }
        },

        drawCallback: function () {
          const api = this.api();
          const pageInfo = api.page.info();
          const totalEntries = pageInfo.recordsTotal;

          const lengthInfoSpan = $(this)
            .closest('.dataTables_wrapper')
            .find('.dataTables_length .custom-length-info');

          if (lengthInfoSpan.length) {
            lengthInfoSpan.text(`Showing ${pageInfo.start + 1} to ${pageInfo.end} of ${totalEntries} entries`);
          }
        }
      };

      if (url) {
        datatableObj = {
          ...datatableObj,
          processing: true,
          serverSide: true,
          pageLength: per_page,
          ajax: {
            url: url,
            data: function (d) {
              if (typeof advanceFilter === 'function' && advanceFilter() !== undefined) {
                d.filter = { ...d.filter, ...advanceFilter() };
              }
            }
          }
        };
      }

      if (data.length) {
        datatableObj = {
          ...datatableObj,
          data: data,
        };
      }

      let datatable = $(tableRef.value).DataTable(datatableObj);

      if (typeof actionCallback === 'function') {
        $(datatable.table().body()).on('click', '[data-table="action"]', function () {
          actionCallback({
            id: $(this).data('id'),
            method: $(this).data('method'),
          });
        });
      }
    }, 0);
  });

  onBeforeUnmount(() => {
    if ($.fn.DataTable.isDataTable(tableRef.value)) {
      $(tableRef.value).DataTable().destroy();
    }
    $(tableRef.value).empty();
  });
};

export default useDataTable;
