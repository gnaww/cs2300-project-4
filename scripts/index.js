function updateForm() {
  if ($("#bg_file").val()) {
    document.getElementById('bg_src').setAttribute('required', '');
    document.getElementById('bg_dsc').setAttribute('required', '');
  }
  else {
   document.getElementById('bg_src').removeAttribute('required');
   document.getElementById('bg_dsc').removeAttribute('required');
  }
}
