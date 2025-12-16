<?php include 'connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pagination</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <h2>Products</h2>

        <?php
            session_start();
            if (isset($_SESSION['message']) && $_SESSION['message'] != '') {
                echo $_SESSION['message'] . "<br>";
                unset($_SESSION['message']);
            }
            ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>FullName</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $rows_result = $conn->query("SELECT id FROM products");
                        $rows_no = $rows_result->num_rows;
                        $rows_per_page = 10;
                        $pages_no = intval(($rows_no - 1) / $rows_per_page) + 1;

                        $page_curent = isset($_GET['p']) ? $_GET['p'] : 1;
                        if (!$page_curent)
                            $page_curent = 1;
                        $start = ($page_curent - 1) * $rows_per_page;

                        $sql = "SELECT id, name, category_id, image, description, price, created, quantity FROM products limit $start,$rows_per_page";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0)
                        {
                            // output data of each row
                            while ($row = $result->fetch_assoc()) {
                    ?>
                                <tr>
                                    <td><?php echo $row['id'] ?></td>
                                    <td><?php echo $row['name'] ?></td>
                                    <td><?php echo $row['category_id'] ?></td>
                                    <td><?php echo $row['image'] ?></td>
                                    <td><?php echo $row['description'] ?></td>
                                    <td><?php echo $row['price'] ?></td>
                                    <td><?php echo $row['created'] ?></td>
                                    <td><?php echo $row['quantity'] ?></td>
                                    <td>
                                        <a href="./edit.php?id=<?php echo $row['id'] ?>">Edit</a><span>|</span> <a href="./delete.php?id=<?php echo $row['id'] ?>">Delete</a>
                                    </td>
                                </tr>
                            <?php
                            }
                        } else {
                            echo "0 results";
                        }
                        ?>
                </tbody>
            </table>

            <?php
                if ($pages_no > 1)
                {
                    echo "Pages: ";
                    if ($page_curent > 1) {
                        echo "<a href='index_pagination.php?p=1' class=\"page\" ><button>First</button></a>&nbsp;&nbsp;";
                        echo "<a href='index_pagination.php?p=" . ($page_curent - 1) . "' class=\"page\"><button>Previous</button>";
                    }
                    echo "<b class=\"page\" >&nbsp;&nbsp;$page_curent</b>&nbsp;&nbsp;";
                    if ($page_curent < $pages_no) {
                        echo "<a href='index_pagination.php?p=" . ($page_curent + 1) . "' class=\"page\"><button>Next</button>&nbsp;&nbsp;";
                        echo "<a href='index_pagination.php?p=$pages_no' class=\"page\" ><button>Last</button></a>&nbsp;&nbsp;";
                    }
                }
            ?>	
    </div>
</body>
</html>


