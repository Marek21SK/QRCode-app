</div>
</main>

<footer class="footer mt-5 my-1 py-1 bg-light-black text-white text-center">
    Copyright &copy; 2024
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<script>
  // Funkcia na nastavenie hodnôt polí v modálnom okne pri jeho otvorení
  $('#previewModal').on('show.bs.modal', function (event) {
      // Získanie hodnoty z príslušných polí vo formulári
      var payment_name = $('#payment_name').val();
      var selectedIban = $('#preview_payment_id').val();
      var iban = $('#payment_id').val();
      var moneytype = $('#moneytype').val();
      var ks = $('#ks').val();
      var sum = $('#sum').val();
      var vs = $('#vs').val();
      var ss = $('#ss').val();
      var dateIban = $('#date_iban').val();
      var infoName = $('#info_name').val();
      var name = $('#name').val();
      var adress = $('#adress').val();
      var adress2 = $('#adress2').val();

      // Nastaviť hodnoty do príslušných polí v modálnom okne
      $('#preview_payment_name').val(payment_name);
      $('#preview_payment_id').val(iban);
      $('#preview_moneytype').val(moneytype);
      $('#preview_ks').val(ks);
      $('#preview_sum').val(sum);
      $('#preview_vs').val(vs);
      $('#preview_ss').val(ss);
      $('#preview_date_iban').val(dateIban);
      $('#preview_info_name').val(infoName);
      $('#preview_name').val(name);
      $('#preview_adress').val(adress);
      $('#preview_adress2').val(adress2);

      // Nastaviť vybranú možnosť pre select element
      $('#preview_payment_id option').each(function() {
          if ($(this).val() == selectedIban) {
              $(this).prop('selected', true);
          }
      });
  });
</script>

</body>
</html>