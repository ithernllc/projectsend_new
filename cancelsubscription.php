<!DOCTYPE html>
<html lang="en">

<body>

  <div class="container mt-2">
    <h2>Cancel Your Subscription</h2>
    <p>If you wish to cancel your subscription, click the button below.</p>
    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#cancelModal">
      Cancel Subscription
    </button>

    <!-- Modal for confirmation -->
    <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="cancelModalLabel">Confirm Cancellation</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            Are you sure you want to cancel your subscription? This action cannot be undone.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <a class="btn btn-danger" href="clients-edit.php?id=<?= $_GET['id'] ?>&action=cancel_subscription">Yes, Cancel Subscription</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
    // Function to get the user_id from the URL
    

    // Function to cancel subscription
    function cancelSubscription(id) {
      console.log(id)

      // Create an AJAX request to cancel the subscription
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "clients-edit.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

      // Prepare data to send (user_id and new subscription status)
      const data = "user_id=" +id + "&subscription_status=canceled";

      // Send the request
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          // Handle response from server
          if (xhr.responseText === "success") {
            alert("Subscription canceled successfully.");
            location.reload(); // Reload the page to reflect changes
          } 
        }
      };
      xhr.send(data);
    }
  </script>
</body>
</html>
