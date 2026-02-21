<?php
session_start();

/* --------------------------
   RESET DATA
---------------------------*/
if (isset($_GET['reset'])) {
    session_destroy();
    header("Location: library.php");
    exit();
}

/* --------------------------
   INITIALIZE DATA (ONLY FIRST TIME)
---------------------------*/
if (!isset($_SESSION['initialized'])) {

    $_SESSION['books'] = [
        1 => ["title" => "Lets learn Python", "author" => "Sandhya", "available" => 3],
        2 => ["title" => "Fundamentals of C", "author" => "Deepak", "available" => 2],
        3 => ["title" => "All about Dsa", "author" => "Preethem", "available" => 1]
    ];

    $_SESSION['members'] = [
        101 => "Manoj",
        102 => "Adarsh",
        103 => "Suhas"
    ];

    $_SESSION['issuedBooks'] = [];
    $_SESSION['initialized'] = true;
}

$books = &$_SESSION['books'];
$members = &$_SESSION['members'];
$issuedBooks = &$_SESSION['issuedBooks'];

/* --------------------------
   ISSUE BOOK
---------------------------*/
if (isset($_POST['issue'])) {

    $memberId = $_POST['member_id'];
    $bookId = $_POST['book_id'];

    if ($books[$bookId]['available'] > 0) {

        $books[$bookId]['available']--;

        $issuedBooks[] = [
            "member_id" => $memberId,
            "book_id" => $bookId,
            "issue_date" => date("Y-m-d"),
            "return_date" => null
        ];

        $message = "Book Issued Successfully!";
        $alertType = "success";
    } else {
        $message = "Book Not Available!";
        $alertType = "danger";
    }
}

/* --------------------------
   RETURN BOOK
---------------------------*/
if (isset($_POST['return'])) {

    $memberId = $_POST['member_id'];
    $bookId = $_POST['book_id'];

    foreach ($issuedBooks as &$record) {

        if (
            $record['member_id'] == $memberId &&
            $record['book_id'] == $bookId &&
            $record['return_date'] == null
        ) {
            $record['return_date'] = date("Y-m-d");
            $books[$bookId]['available']++;
            $message = "Book Returned Successfully!";
            $alertType = "info";
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Library Book Issue Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

    <div class="text-center mb-4">
        <h2 class="fw-bold">📚 Library Book Issue Tracker</h2>
        <a href="?reset=true" class="btn btn-sm btn-outline-danger mt-2">Reset Data</a>
    </div>

    <?php if (isset($message)): ?>
        <div class="alert alert-<?= $alertType ?> alert-dismissible fade show" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">

        <!-- ISSUE BOOK -->
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    Issue Book
                </div>
                <div class="card-body">
                    <form method="post">

                        <div class="mb-3">
                            <label class="form-label">Member</label>
                            <select name="member_id" class="form-select">
                                <?php foreach ($members as $id => $name): ?>
                                    <option value="<?= $id ?>"><?= $name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Book</label>
                            <select name="book_id" class="form-select">
                                <?php foreach ($books as $id => $book): ?>
                                    <option value="<?= $id ?>">
                                        <?= $book['title'] ?> (Available: <?= $book['available'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" name="issue" class="btn btn-primary w-100">
                            Issue Book
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- RETURN BOOK -->
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    Return Book
                </div>
                <div class="card-body">
                    <form method="post">

                        <div class="mb-3">
                            <label class="form-label">Member</label>
                            <select name="member_id" class="form-select">
                                <?php foreach ($members as $id => $name): ?>
                                    <option value="<?= $id ?>"><?= $name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Book</label>
                            <select name="book_id" class="form-select">
                                <?php foreach ($books as $id => $book): ?>
                                    <option value="<?= $id ?>">
                                        <?= $book['title'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" name="return" class="btn btn-success w-100">
                            Return Book
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- AVAILABLE BOOKS -->
    <div class="card shadow mb-4">
        <div class="card-header bg-dark text-white">
            Available Books
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-secondary">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Available</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($books as $id => $book): ?>
                    <tr>
                        <td><?= $id ?></td>
                        <td><?= $book['title'] ?></td>
                        <td><?= $book['author'] ?></td>
                        <td><span class="badge bg-primary"><?= $book['available'] ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ISSUED BOOKS -->
    <div class="card shadow">
        <div class="card-header bg-warning">
            Issued Books
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-secondary">
                <tr>
                    <th>Member</th>
                    <th>Book</th>
                    <th>Issue Date</th>
                    <th>Return Date</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($issuedBooks as $record): ?>
                    <tr>
                        <td><?= $members[$record['member_id']] ?></td>
                        <td><?= $books[$record['book_id']]['title'] ?></td>
                        <td><?= $record['issue_date'] ?></td>
                        <td>
                            <?= $record['return_date'] 
                                ? "<span class='badge bg-success'>".$record['return_date']."</span>"
                                : "<span class='badge bg-danger'>Not Returned</span>" ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>