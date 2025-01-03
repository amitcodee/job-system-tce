<?php
session_start();
include 'common-header.php';
include 'header.php';
include 'sidenav.php';
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must be logged in to access this page.'); window.location.href='login.php';</script>";
    exit();
}
?>

<main id="main" class="main">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Document Collection</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                <i class="bi bi-plus-circle"></i> Add Document
            </button>
        </div>

        <!-- Documents Table -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Sno.</th>
                        <th>Document Name</th>
                        <th>Uploaded File</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch user documents from the database
                    $userId = $_SESSION['user_id'];
                    $stmt = $conn->prepare("SELECT id, name, file_path FROM user_documents WHERE user_id = ?");
                    $stmt->bind_param("i", $userId);
                    $stmt->execute();
                    $documents = $stmt->get_result();

                    if ($documents->num_rows > 0) {
                        $counter = 1;
                        while ($document = $documents->fetch_assoc()) {
                            echo "<tr>
                                <td>{$counter}</td>
                                <td>" . htmlspecialchars($document['name']) . "</td>
                                <td><a href='{$document['file_path']}' target='_blank'>View Document</a></td>
                                <td>
                                    <a href='delete-document.php?id={$document['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this document?\");'>
                                        <i class='bi bi-trash'></i>
                                    </a>
                                </td>
                            </tr>";
                            $counter++;
                        }
                    } else {
                        echo "<tr><td colspan='4'>No documents uploaded yet.</td></tr>";
                    }

                    $stmt->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Add Document Modal -->
<div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="upload-document.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDocumentModalLabel">Add Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="document_name" class="form-label">Document Name *</label>
                        <input type="text" id="document_name" name="document_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="document_file" class="form-label">Choose File *</label>
                        <input type="file" id="document_file" name="document_file" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload Document</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include 'footer.php';
include 'common-footer.php';
?>
