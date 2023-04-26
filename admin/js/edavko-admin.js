(function ($) {
  'use strict';

  $(document).ready(function ($) {
    $('#edavko-verify-invoice-form').submit(function (event) {
      event.preventDefault();

      // Check if either "zoi" or "eor" is present
      var zoi = $('#edavko_verify_invoice_zoi').val();
      var eor = $('#edavko_verify_invoice_eor').val();

      if (zoi && eor) {
        // If both "zoi" and "eor" are present, use "eor"
        var url =
          'http://studentdocker.informatika.uni-mb.si:49163/check-invoice?eor=' +
          eor;
      } else if (zoi) {
        // If only "zoi" is present, use "zoi"
        var url =
          'http://studentdocker.informatika.uni-mb.si:49163/check-invoice?zoi=' +
          zoi;
      } else if (eor) {
        // If only "eor" is present, use "eor"
        var url =
          'http://studentdocker.informatika.uni-mb.si:49163/check-invoice?eor=' +
          eor;
      } else {
        // If neither "zoi" nor "eor" is present, display a warning
        $('#edavko-verify-invoice-result').html(
          '<p>Vnesite "ZOI" ali "EOR"!</p>',
        );
        return;
      }

      $('#edavko-verify-invoice-result').html('<p>Obdelujem zahtevo ...</p>');

      $.ajax({
        type: 'GET',
        url: url,
        headers: {
          Authorization: 'Bearer 1002376637',
        },
        success: function (response) {
          $('#edavko-verify-invoice-result').html('<p>Račun je veljaven.</p>');
        },
        error: function (xhr, status, error) {
          $('#edavko-verify-invoice-result').html(
            '<p>Napaka pri obdelavi zahteve: ' + error + status + '</p>',
          );
        },
      });
    });
  });

  $(document).ready(function ($) {
    $('#edavko-verify-business-space-form').submit(function (event) {
      event.preventDefault();

      var id = $('#edavko_verify_business_space').val();

      if (!id) {
        $('#edavko-verify-business-space-result').html(
          '<p>Vnesite ID poslovnega prostora!</p>',
        );
        return;
      }

      $('#edavko-verify-business-space-result').html(
        '<p>Obdelujem zahtevo ...</p>',
      );

      $.ajax({
        type: 'GET',
        url:
          'http://studentdocker.informatika.uni-mb.si:49163/check-premise?id=' +
          id,
        headers: {
          Authorization: 'Bearer 1002376637',
        },
        success: function (response) {
          $('#edavko-verify-business-space-result').html(
            '<p>Poslovni prostor je veljaven: ' + response + '</p>',
          );
        },
        error: function (xhr, status, error) {
          $('#edavko-verify-business-space-result').html(
            '<p>Napaka pri obdelavi zahteve: ' + error + status + '</p>',
          );
        },
      });
    });
  });
})(jQuery);
