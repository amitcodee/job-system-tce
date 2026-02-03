<?php
if (isset($conn) && $conn instanceof mysqli) {
  try {
    $conn->close();
  } catch (Throwable $e) {
    // Ignore errors if the connection was already closed.
  }
}
?>
 <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="https://kit.fontawesome.com/0fffda5efb.js" crossorigin="anonymous"></script>
  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>