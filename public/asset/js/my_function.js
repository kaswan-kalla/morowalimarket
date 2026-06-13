function formatNumber(num) {
  if (num === '' || num === undefined || num === null) {
    return '.';
  }
  return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
}
function formatNumber2(num) {
  if (num === '' || num === undefined || num === null) {
    return '.';
  }
  return Intl.NumberFormat('id-ID').format(num);
}

function time(str) {
  if (str == '' || str == undefined) {
    return '.';
  }
  let time = str.substr(0, 5);
  return time;
}

/**
 * Check if a keyCode should be ignored for search input (navigation/modifier keys)
 * @param {number} keyCode - The event.keyCode
 * @returns {boolean} true if the key should be ignored
 */
function isIgnoredKey(keyCode) {
  // Arrow keys (37-40), Tab (9), Shift (16), Ctrl (17), Alt (18),
  // CapsLock (20), Escape (27), PageUp/Down (33-34), End/Home (35-36)
  var ignoredKeys = [9, 16, 17, 18, 20, 27, 33, 34, 35, 36, 37, 38, 39, 40];
  return ignoredKeys.indexOf(keyCode) !== -1;
}

function delay(callback, ms) {
  var timer = 0;
  return function () {
    var context = this,
      args = arguments;
    if (context.value.length > 0) {
      $ms = ms;
    } else {
      $ms = 0;
    }

    clearTimeout(timer);
    timer = setTimeout(function () {
      callback.apply(context, args);
    }, $ms || 0);
  };
}

function keyescape(e) {
  var $val = e.target.value;
  var code = e.keyCode || e.which;
  var key = [16, 17, 18, 20, 37, 38, 39, 40, 46, 32];
  // console.log(code);

  if ($val.length > 0) {
    var $flag = $.inArray(code, key) >= 0 ? false : true;
    return $flag;
  } else {
    return true;
  }
}
class pesanInfo {
  constructor(title, subtitle) {
    this.title = title;
    this.subtitle = subtitle;
    Swal({
      type: 'success',
      title: this.title,
      text: this.subtitle,
    });
  }
}

class pesanHasil {
  constructor(type) {
    this.type = type;
    const toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      background: 'white',
      width: '15rem',
      showConfirmButton: false,
      timer: 2000,
    });
    return toast({
      type: 'success',
      title: 'Data ' + this.type,
    });
  }
}
//    alert hapus data

function pesanPeringatan(subtitle) {
  Swal.fire({
    text: subtitle,
    type: 'warning',
    showCancelButton: false,
    cancelButtonColor: '#d33',
    confirmButtonText: 'OK, Tambahkan nanti',
  });
}

class pesanCallback {
  constructor(title, text, callback, callbackCancel = false) {
    Swal({
      title: title,
      html: text,
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'OK',
    }).then((result) => {
      if (result.value) {
        callback();
      } else {
        callbackCancel();
      }
    });
  }
}
class pesanHapus {
  constructor(data, url, callback, $element, tangki_type, open_date) {
    this.data = data;
    this.url = url;
    this.element = $element;
    var el = this.element ? this.element : '#tableData';
    var tangki_type = tangki_type ? tangki_type : '';
    Swal({
      title: 'Anda yakin?',
      text: 'data akan dihapus!',
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Hapus',
    }).then((result) => {
      if (result.value) {
        $(el).jtable('deleteRecord', {
          key: this.data,
          url: this.url,
          success: function (data) {
            var pesan = new pesananHasil('Dihapus');
            callback(tangki_type, open_date);
          },
          error: function (err) {
            console.log(err);
          },
        });
      }
    });
  }
}

class pesanError {
  constructor(data, elementIdToFocus) {
    this.data = data;
    this.elementIdToFocus = elementIdToFocus;

    Swal({
      type: 'error',
      title: 'Oops...',
      html: this.data,
      keydownListenerCapture: true,
      stopKeydownPropagation: true,
    }).then((result) => {
      if (result.value && this.elementIdToFocus) {
        // Jika tombol OK ditekan dan elementIdToFocus ada
        setTimeout(() => {
          const element = document.getElementById(this.elementIdToFocus);
          if (element) {
            element.select();
          }
        }, 500);
      }
    });
  }
}

function marker($el = '#cari', $contanier = '#tableData') {
  let $val = $($el).val();
  console.log($val);

  let search_by = $('#searchBy').val();
  let options = {
    element: 'span',
    className: 'mark',
    separateWordSearch: false,
  };
  var $ctx = $(`${$contanier}`).find('table.jtable tr td');

  // $ctx = $('table.jtable tr td');
  if ($($el).length > 0 && $($el).val() !== null) {
    $ctx.unmark({
      done: function () {
        $ctx.mark($val.split(' '), options);
      },
    });
  }
}

function toNumber(val) {
  // Handle null, undefined, or empty values
  if (val === null || val === undefined || val === '') return 0;

  // If already a number, return it
  if (typeof val === 'number') return val;

  // Convert to string if needed
  if (typeof val !== 'string') return Number(val);

  let s = val.trim();

  // Handle special case where formatNumber returns '.' for empty values
  if (s === '.') return 0;

  // Jika ada titik dan koma → deteksi otomatis
  if (s.includes('.') && s.includes(',')) {
    // Jika koma di belakang → koma = desimal → format Indo
    if (s.lastIndexOf(',') > s.lastIndexOf('.')) {
      s = s.replace(/\./g, ''); // hapus thousand separator
      s = s.replace(',', '.'); // ubah decimal
    } else {
      // Titik = desimal, koma = thousand
      s = s.replace(/,/g, ''); // hapus thousand separator
    }
  }

  // Jika hanya mengandung koma
  else if (s.includes(',')) {
    const parts = s.split(',');

    // Jika koma diikuti 3 digit → koma = thousand separator
    if (parts[1] && parts[1].length === 3) {
      s = s.replace(/,/g, ''); // hapus thousand
    } else {
      // Jika desimal
      s = s.replace(',', '.');
    }
  }

  // Jika hanya titik → bisa jadi thousand atau decimal
  else if (s.includes('.')) {
    const parts = s.split('.');

    if (parts[1] && parts[1].length === 3) {
      // 4.500 → thousand separator
      s = s.replace(/\./g, '');
    }
    // else titik = decimal → biarkan
  }

  const result = Number(s);
  // Return 0 if conversion results in NaN
  return isNaN(result) ? 0 : result;
}

function printGlobal(element) {
  $(element).printThis({
    debug: false, // show the iframe for debugging
    importCSS: true, // import parent page css
    importStyle: true, // import style tags
    printContainer: false, // print outer container/$.selector
    loadCSS: '', // path to additional css file - use an array [] for multiple
    pageTitle: false, // add title to print page
    removeInline: false, // remove inline styles from print elements
    removeInlineSelector: '*', // custom selectors to filter inline styles. removeInline must be true
    printDelay: 333, // variable print delay
    header: null, // prefix to html
    footer: null, // postfix to html
    base: false,
  });
}
hideValidation = () => {
  $('.validation').text('');
};

updateAnimation = ($tableRow) => {
  $($tableRow)
    .stop(true, true)
    .addClass('my-row-updated-text', 'slow', '', function () {
      $($tableRow).removeClass('my-row-updated-text', 'slow');
    });
};
createAnimation = ($tableRow) => {
  // $($tableRow)
  //   .stop(true, true)
  //   .addClass("my-row-updated", "slow", "", function () {
  //     $($tableRow).removeClass("my-row-updated my-row-updated-text", 2500);
  //   });
  $($tableRow).css({
    'background-color': 'slategray',
    color: 'transparent',
  });
  $($tableRow).animate(
    {
      backgroundColor: 'transparent',
      color: 'black',
    },
    2000,
  );
};

userAction = (action, oldValue, newValue, status) => {
  var data = { action: action };
  if (oldValue !== undefined)
    data.old_value =
      typeof oldValue === 'string' ? oldValue : JSON.stringify(oldValue);
  if (newValue !== undefined)
    data.new_value =
      typeof newValue === 'string' ? newValue : JSON.stringify(newValue);
  if (status !== undefined) data.status = status;
  $.ajax({
    type: 'post',
    dataType: 'json',
    url: base_url('userAction'),
    data: data,
  });
};

// === Global JS Error Tracker ===
window.addEventListener('error', function (e) {
  var msg = e.message || 'Unknown JS error';
  var src = e.filename || '';
  var line = e.lineno || 0;
  userAction('JS Error: ' + msg, null, { source: src, line: line }, 'error');
});

// === AJAX Error Tracker — dijalankan setelah jQuery siap ===
(function () {
  function initAjaxErrorTracker() {
    if (typeof jQuery === 'undefined') {
      setTimeout(initAjaxErrorTracker, 200);
      return;
    }
    jQuery(document).ajaxError(function (event, jqXHR, settings, thrownError) {
      if (settings.url && settings.url.indexOf('userAction') === -1) {
        userAction(
          'AJAX Error: ' + settings.url,
          null,
          { status: jqXHR.status, error: thrownError },
          'error',
        );
      }
    });
  }
  initAjaxErrorTracker();
})();

// === Click Tracker — NONAKTIF (menyebabkan tabel log bengkak) ===
// $(document).on('click', '.btn, button:not([type="submit"]):not(.close):not(.ui-button)', function (e) {
//   var btn = $(this);
//   var label = btn.text().trim() || btn.attr('title') || btn.attr('id') || 'button';
//   if (label.length > 50) label = label.substring(0, 50);
//   var now = Date.now();
//   if (window._lastClickLog && (now - window._lastClickLog) < 1000) return;
//   window._lastClickLog = now;
//   userAction('click: ' + label);
// });

makeGrafik = (data, element) => {
  $(element).jqBarGraph({
    data: data,
    colors: ['#28a745', '#dc3545'],
    title: false, // title of your graph, accept HTML
    barSpace: 10, // this is default space between bars in pixels
    width: '', // default width of your graph
    height: 500, //default height of your graph
    color: '#000000', // if you don't send colors for your data this will be default bars color
    lbl: '', // if there is no label in your array
    sort: false, // sort your data before displaying graph, you can sort as 'asc' or 'desc'
    position: 'bottom', // position of your bars, can be 'bottom' or 'top'. 'top' doesn't work for multi type
    prefix: '', // text that will be shown before every label
    postfix: '', // text that will be shown after every label
    animate: true, // if you don't need animated appearance change to false
    speed: 2, // speed of animation in seconds
    legendWidth: 100, // width of your legend box
    legend: false, // if you want legend change to true
    legends: true, // array for legend. for simple graph type legend will be extracted from labels if you don't set this
    type: '', // for multi array data default graph type is stacked, you can change to 'multi' for multi bar type
    showValues: true, // you can use this for multi and stacked type and it will show values of every bar part
    showValuesColor: '#fff', // color of font for values
  });
};
function intNoDoc() {
  return parseInt($('#no_doc').val().match(/\d+/)[0], 10);
}

function formatNoDoc(doctype, nodoc) {
  return doctype + String(nodoc).padStart(7, '0');
}
function formatNoDocInt(nodoc = false) {
  if (nodoc) {
    return parseInt(nodoc.match(/\d+/)[0], 10);
  }
}
function formatNoDocSPB(spb, divisi = false, nodoc = false) {
  if (!isNaN(Number(spb) * 0)) {
    if (divisi == null) return '.';
    else {
      return `SPB/${divisi.toLocaleUpperCase()}/${spb.padStart(4, '0')}`;
    }
  } else {
    if (nodoc) {
      return nodoc;
    } else {
    }
    return spb;
  }
}

function nomorSuratRomawi(nodoc, strDate) {
  // Array nama bulan dalam angka romawi
  const romawi = [
    'I',
    'II',
    'III',
    'IV',
    'V',
    'VI',
    'VII',
    'VIII',
    'IX',
    'X',
    'XI',
    'XII',
  ];

  if (!nodoc || !strDate) return '';

  let bulan = parseInt(moment(strDate).format('MM'), 10);
  let tahun = moment(strDate).format('YYYY');
  let noDocStr = String(nodoc).padStart(3, '0');

  // Pastikan bulan valid
  if (bulan < 1 || bulan > 12) return '';

  return `${noDocStr}/INV/PMS/${romawi[bulan - 1]}/${tahun}`;
}

function setNoDoc(doctype, next = false) {
  let nodoc;

  $.ajax({
    type: 'post',
    dataType: 'json',
    url: base_url(`GetNoDoc/index`),
    data: { doctype },
    success: function (data) {
      console.log(data);

      // Handle case when data is null or undefined
      if (!data) {
        console.error('Response data is null or undefined');
        nodoc = 0;
      } else if (data == 0 || data.no_doc == 0) {
        nodoc = 0;
      } else if (typeof data === 'object' && data.no_doc !== undefined) {
        nodoc = data.no_doc;
      } else if (typeof data === 'number') {
        nodoc = data;
      } else {
        console.warn('Unexpected data format:', data);
        nodoc = 0;
      }

      if (next) nodoc++;

      $('#no_doc').val(formatNoDoc(doctype, nodoc));
    },
    error: function (xhr, status, error) {
      console.error('Error fetching no_doc:', {
        xhr: xhr,
        status: status,
        error: error,
        responseText: xhr.responseText,
      });

      // Fallback to default value
      nodoc = 0;
      if (next) nodoc++;

      $('#no_doc').val(formatNoDoc(doctype, nodoc));

      // Show user-friendly message (optional)
      // alert('Gagal mengambil nomor dokumen. Menggunakan nomor default.');
    },
    complete: function () {
      console.log('GetNoDoc request completed');
    },
  });
}

function myRequireForm(element) {
  // $(`${element} .btn-primary`).prop("disabled", true);
  let temp = [];
  $(`${element} .my-require`).each((i, e) => {
    if ($(e).val() == '') {
      temp = [...temp, 1];
    }
    if (temp.length > 0) {
      $(`${element} .modal-footer .btn-primary`)
        .not('.modal .btn-enabled')
        .prop('disabled', true);
    } else {
      $(`${element} .modal-footer .btn-primary`).prop('disabled', false);
    }
  });
  // console.log(temp);
}
function myRequireTrigger(elemen) {
  $('.modal .my-require').on('change keyup', () => {
    setTimeout(() => {
      myRequireForm(elemen);
    }, 200);
  });
}

function qrGenerate(text, pref, e = '') {
  let el = 'qrcode';
  if (e == '') {
    $('#qrcode').children().remove();
  } else {
    el = e;
    $('#' + e)
      .children()
      .remove();
  }

  var qrcode = new QRCode(document.getElementById(el), {
    text: text, // Content

    width: 84, // Widht
    height: 84, // Height
    quietZone: 0,
    colorDark: '#000000', // Dark color
    // colorLight: "#FFFACD", // Light color

    // === Title
    title: '', // Title
    titleFont: 'bold 10px Arial', // Title font
    titleColor: '#004284', // Title Color
    titleBackgroundColor: '#fff', // Title Background
    titleHeight: 0, // Title height, include subTitle
    titleTop: 0, // Title draw position(Y coordinate), default is 30

    // === SubTitle
    subTitle: '', // Subtitle content
    subTitleFont: '8px Arial', // Subtitle font
    subTitleColor: '#004284', // Subtitle color
    subTitleTop: 40, // Subtitle drwa position(Y coordinate), default is 50

    // === Logo
    logo: base_url('asset/img/logo.jpg'), // LOGO
    //logo:"http://127.0.0.1:8020/easy-qrcodejs/demo/logo.png",
    logoWidth: 20,
    logoHeight: 20,
    // logoBackgroundColor: '#ffffff', // Logo backgroud color, Invalid when `logBgTransparent` is true; default is '#ffffff'
    logoBackgroundTransparent: true, // Whether use transparent image, default is false

    // === Posotion Pattern(Eye) Color
    // PO: '#e1622f', // Global Position Outer color. if not set, the defaut is `colorDark`
    // PI: '#aa5b71', // Global Position Inner color. if not set, the defaut is `colorDark`
    // //					PO_TL:'', // Position Outer - Top Left
    // PI_TL: '#b7d28d', // Position Inner - Top Left
    // PO_TR: '#aa5b71', // Position Outer - Top Right
    // PI_TR: '#c17e61', // Position Inner - Top Right
    // //					PO_BL:'', // Position Outer - Bottom Left
    // //					PI_BL:'' // Position Inner - Bottom Left
    // //					PO_BR:'', // Position Outer - Bottom Right
    // //					PI_BR:'' // Position Inner - Bottom Right

    // // === Timing Pattern Color
    // //	timing: '#e1622f', // Global Timing color. if not set, the defaut is `colorDark`
    // timing_H: '#ff6600', // Horizontal timing color
    // timing_V: '#cc0033', // Vertical timing color

    // // === Aligment color
    // AI:'#27408B',
    // AO:'#7D26CD',

    // correctLevel: QRCode.CorrectLevel.L, // L, M, Q, H
    dotScale: 0.1,
  });
}

function busy_message(elm, text = 'Muat data...') {
  $(elm + ' .jtable-busy-message:first').text(text);
  $(elm + ' .jtable-busy-message:first').show();
}
function busy_message_hide(elm) {
  $(elm + ' .jtable-busy-message:first').hide();
}

function exportData(url, file, data = false) {
  busy_message('#tableData', 'Mengunduh file...');
  let dateFrom = $('#tanggal_dari').val();
  let dateTo = $('#tanggal_sampai').val();

  // --- progress bar setup (simulated, server sends no Content-Length) ---
  var $wrap = $('#export-progress-wrap');
  var $bar = $('#export-progress-bar');
  var $pct = $('#export-progress-pct');
  var $label = $('#export-progress-label');
  var $btn = $('#export-periode');
  var _timer = null;
  var _fakePct = 0;

  // Scale tick speed by date range: more days = slower progress
  var _days = 30; // default
  if (dateFrom && dateTo) {
    var _ms = new Date(dateTo) - new Date(dateFrom);
    _days = Math.max(1, Math.round(_ms / 86400000));
  }
  // interval: 200ms (≤30 days) → 800ms (≥365 days), capped
  var _interval = Math.min(
    800,
    Math.max(200, Math.round(200 + (_days / 365) * 600)),
  );
  // step divisor: wider range = smaller steps per tick
  var _stepScale = Math.max(0.3, Math.min(1, 30 / _days));

  function _startProgress() {
    if (!$wrap.length) return;
    _fakePct = 0;
    $wrap.css({ 'border-color': '#dee2e6', background: '#fff' }).show();
    $bar
      .removeClass('bg-danger')
      .addClass('bg-primary progress-bar-striped progress-bar-animated');
    $label
      .removeClass('text-danger')
      .addClass('text-muted')
      .html('<i class="fa fa-download mr-1"></i>Mengunduh file...');
    $pct.removeClass('text-danger').addClass('text-muted').text('0%');
    $bar.css('width', '0%').attr('aria-valuenow', 0);
    $btn.prop('disabled', true);
    // Tick: fast early, slow near 90%, scaled by date range
    _timer = setInterval(function () {
      var step =
        (_fakePct < 30 ? 4 : _fakePct < 60 ? 2 : _fakePct < 80 ? 1 : 0.3) *
        _stepScale;
      _fakePct = Math.min(90, _fakePct + step);
      var p = Math.round(_fakePct);
      $bar.css('width', p + '%').attr('aria-valuenow', p);
      $pct.text(p + '%');
    }, _interval);
  }

  function _finishProgress(success) {
    if (!$wrap.length) return;
    clearInterval(_timer);
    if (success) {
      $bar.css('width', '100%').attr('aria-valuenow', 100);
      $pct.text('100%');
      setTimeout(function () {
        $wrap.hide();
        $bar.css('width', '0%').attr('aria-valuenow', 0);
        $pct.text('0%');
        $btn.prop('disabled', false);
      }, 800);
    } else {
      // Error state: red bar, × icon — stays until user closes manually
      $bar
        .removeClass('bg-primary progress-bar-striped progress-bar-animated')
        .addClass('bg-danger')
        .css('width', '100%')
        .attr('aria-valuenow', 100);
      $wrap.css({ 'border-color': '#f5c6cb', background: '#fff5f5' });
      $label
        .removeClass('text-muted')
        .addClass('text-danger')
        .html('<i class="fa fa-times-circle mr-1"></i>Gagal mengunduh!');
      $pct
        .removeClass('text-muted')
        .addClass('text-danger')
        .html(
          '<span id="export-close-btn" style="cursor:pointer;font-size:16px;line-height:1;" title="Tutup">&times;</span>',
        );
      $('#export-close-btn').on('click', function () {
        $wrap.hide();
        $bar.css('width', '0%');
      });
      $btn.prop('disabled', false);
    }
  }
  // --------------------------------------------------------------------

  _startProgress();

  $.ajax({
    url: base_url(`exportData/${url}`),
    method: 'GET',

    xhrFields: {
      responseType: 'blob',
    },
    success: function (response) {
      _finishProgress(true);

      let blob = new Blob([response], { type: 'application/octet-stream' });
      let link = document.createElement('a');
      link.href = URL.createObjectURL(blob);
      let bulan, tahun;
      if ($('#bulan').is(':visible')) {
        bulan = $('#bulan').val().padStart(2, 0); // Buat bulan di digit
        tahun = $('#tahun').val().padStart(4, 0);
      } else {
        bulan = ('0' + (new Date().getMonth() + 1)).slice(-2); // Buat bulan di digit
        tahun = new Date().getFullYear();
      }
      link.download = `${file} ${dateFrom}-${dateTo}.xlsx`; // Ganti sesuai file yang dihasilkan
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);

      busy_message_hide('#tableData');
    },
    error: function () {
      console.error('Gagal mengunduh file');
      _finishProgress(false);
      busy_message_hide('#tableData');
    },
  });
}

function konversiStok(stok, konversi) {
  if (stok == 0 || stok == null) {
    return '<span style="color:gainsboro">0</span>';
  } else if (konversi != 0 && konversi != null) {
    return Math.floor(stok / konversi);
  } else {
    return stok;
  }
}

function roundNumber(num, decimals = 0) {
  if (num === null || num === undefined || num === '') return 0;
  const factor = Math.pow(10, decimals);
  return Math.round(num * factor) / factor;
}

/**
 * Creates a two-column layout for jTable edit forms
 * Distributes form fields evenly between two columns
 * @param {Object} data - The jTable formCreated event data
 */
function createJTableTwoColumnLayout(data, columns = true) {
  // Create a two-column layout for the edit form
  if (data.formType === 'edit') {
    // Wrap fields in a row with two columns
    var $form = data.form;
    var $fields = $form.children('.jtable-input-field-container');

    // Add 'form-control' class to all input elements within the fields
    $fields.find('input, select, textarea').addClass('form-control');

    // Create row structure
    var $row = $('<div class="row"></div>');
    var $col1 = $('<div class="col-md-6"></div>');
    var $col2 = $('<div class="col-md-6"></div>');

    if (columns) {
      $fields.each(function (index) {
        if (index % 2 === 0) {
          $col1.append(this);
        } else {
          $col2.append(this);
        }
        $row.append($col1).append($col2);
        $form.empty().append($row);
      });
    }
    // Distribute fields between columns
  }
}
