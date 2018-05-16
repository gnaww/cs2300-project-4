$(document).ready(function() {
  // Submit AJAX request when form is submitted
  $("#search-members").on("submit", function(event) {
    // Don't allow the form to submit normally
    event.preventDefault();
    // Send an AJAX request for the search
    var params = $("#search-members").serialize();
    $.get('members_search.php', params, function(data){
      // Update search results section
      $("#search-results").html(data);
      // Add GET params to URL
      history.pushState(null, null, "members.php?" + params);
    }, 'html');
  });

});
