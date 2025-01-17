<?php
    session_start();

    // If the user is not logged in, redirect to login page
    if(!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit();
    }

    // Assign the session user to a variable
    $user = $_SESSION['user'];
    $users = include('database/show-users.php');
    $items = include('database/show-items.php');
    include './database/userData.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/57b929fbcb.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./CSS/sidebar.css">
    <link rel="stylesheet" href="./public/itemdashboard.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <title>User Items Management</title>
</head>

<body>
    <div id="main-container">
        <!-- Sidebar -->
        <?php include './partials/sidebar.php';?>

        <!-- Main Content Area -->
        <div class="main-content">
            <div class="dashboard_topbar">
                <a><i class="fa fa-navicon"></i></a>
                <a><h2>User Main Content</h2></a>
                <a href="./database/logout.php" class="logout"><i class="fa fa-power-off"></i>Logout</a>
            </div>

            <div class="content-area">
                <!--add items -->
                <div class="add-item-container">
                        <h2>Add Items</h2>
                        <form class="add-item-form" action="database/createitem.php" method="POST"> 
                            <input type="text" name="item_name" placeholder="Item Name" id="item_name" required />
                            <input type="number" name="quantity" placeholder="Quantity" id="quantity" min="1" required />
                            <input type="number" name="price" placeholder="Price" id="price" step="0.01" min="0" required />
                            <input type="text" name="item_description" placeholder="Description" id="item_description" required 
                            style="width: 90%; height: 80px;">
                                 <input type="submit" value="Add Item" />
                        </form> 
                        <?php
                            if (isset($_SESSION['response'])) { 
                                $responseMessage = $_SESSION['response']['message'];
                                $is_success = $_SESSION['response']['success']; // Fixed the typo from 'sucess' to 'success'
                            ?>

                            <div class="error-message" id="error-message">
                                <p class="error_message <?= $is_success ? 'error_message_True' : 'error_message_False' ?>">
                                    <?= htmlspecialchars($responseMessage) ?>
                                </p>
                            </div>

                            <?php 
                                unset($_SESSION['response']); 
                            } 
                            ?>

                    </div>

                        <!-- Display Items list -->
                    <div class="items-container">
                       <!-- Search Bar -->
                        <div class="search-container">
                            <input class="search-input" Type="text" id="search-input" placeholder="Search items..." onkeyup="searchItems()" />
                            <button class="search-button" id="search-button" onclick="searchItems()">Search</button>
                        
                        <div class="Results-message" id="no-results-message" style="display:none;">No items found for "<span id="search-term"></span>".</div>
                        </div>
                       
                        <h3>Items List</h3>
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Discription</th>
                                    <th>Account Created At</th>
                                    <th>Account Updated At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="items-table-body">
                                    <?php foreach($items as $index => $item){ ?>
                                <tr>
                                    <td><?=$index+1?></td>
                                    <td><?=$item['item_name']?></td>
                                    <td><?=$item['price']?></td>
                                    <td><?=$item['quantity']?></td>
                                    <td><?=$item['item_description']?></td>
                                    <td><?=date('M d, y @ h:i:s A', strtotime($item['created_at']))?></td>
                                    <td><?=date('M d, y @ h:i:s A', strtotime($item['updated_at']))?></td>
                                    <td>
                                    <div class="action-buttons-container">
                                        <button class="action-button edit-button" onclick="openEditModal(<?= $item['item_id'] ?>, '<?= htmlspecialchars($item['item_name']) ?>', '<?= htmlspecialchars($item['item_description']) ?>', <?= $item['price'] ?>, <?= $item['quantity'] ?>)">
                                            <i class="fas fa-camera"></i> Edit
                                        </button>
                                        <a href="#" class="action-button delete-button" onclick="deleteItem(<?= $item['item_id'] ?>, '<?= htmlspecialchars($item['item_name']) ?>')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>

                                </tr>
                                    <?php } ?>
                            </tbody>
                        </table>
                        <!-- Edit Item Modal -->
                        <div id="editItemModal" style="display:none;">
                            <div class="modal-content">
                                <span class="close" onclick="closeEditModal()">&times;</span>
                                <h2>Edit Item</h2>
                                <form id="editItemForm" onsubmit="return false;">
                                    <input type="hidden" id="edit_item_id" name="item_id">
                                    <label for="edit_item_name">Item Name:</label>
                                    <input type="text" id="edit_item_name" name="item_name" required>
                                    <label for="edit_item_description">Description:</label>
                                    <textarea id="edit_item_description" name="item_description" required></textarea>
                                    <label for="edit_item_price">Price:</label>
                                    <input type="number" id="edit_item_price" name="item_price" required>
                                    <label for="edit_item_quantity">Quantity:</label>
                                    <input type="number" id="edit_item_quantity" name="item_quantity" required>
                                    <button type="submit" onclick="submitEdit()">Save Changes</button>
                                </form>
                            </div>
                        </div>

                    </div>
                
            </div>
        </div>
    </div>

</body>

<script>
function searchItems() {
    var input = document.getElementById("search-input").value.toLowerCase();
    var tableBody = document.getElementById("items-table-body");
    var rows = tableBody.getElementsByTagName("tr");
    var noResultsMessage = document.getElementById("no-results-message");
    var searchTerm = document.getElementById("search-term");

    var hasResults = false;

    for (var i = 0; i < rows.length; i++) {
        var cells = rows[i].getElementsByTagName("td");
        if (cells.length > 0) {
            var itemName = cells[1].textContent.toLowerCase();
            if (itemName.includes(input) || input.trim() === "") {
                rows[i].style.display = ""; // Show matching row
                hasResults = true; // There are results
            } else {
                rows[i].style.display = "none"; // Hide non-matching row
            }
        }
    }

    // Display no results message if no matches found
    if (!hasResults && input.trim() !== "") {
        searchTerm.textContent = input; // Set the search term
        noResultsMessage.style.display = "block"; // Show message
    } else {
        noResultsMessage.style.display = "none"; // Hide message if there are results
    }
}

function deleteItem(itemId, itemName) {
    if (confirm(`Are you sure you want to delete "${itemName}"?`)) {
        // Create the data to be sent
        const data = new FormData();
        data.append('item_id', itemId);
        data.append('item_name', itemName);
        
        // Make the AJAX request
        fetch('database/delete-item.php', {
            method: 'POST',
            body: data
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message); // Show success message
                // Refresh the page after successful deletion
                location.reload();
            } else {
                alert(data.message); // Show error message
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the item.');
        });
    }
}
function openEditModal(itemId, itemName, itemDescription, itemPrice, itemQuantity) {
    document.getElementById('edit_item_id').value = itemId;
    document.getElementById('edit_item_name').value = itemName;
    document.getElementById('edit_item_description').value = itemDescription;
    document.getElementById('edit_item_price').value = itemPrice;
    document.getElementById('edit_item_quantity').value = itemQuantity;
    
    document.getElementById('editItemModal').style.display = 'block'; // Show the modal
}

function closeEditModal() {
    document.getElementById('editItemModal').style.display = 'none'; // Hide the modal
}
function submitEdit() {
    const itemId = document.getElementById('edit_item_id').value;
    const itemName = document.getElementById('edit_item_name').value;
    const itemDescription = document.getElementById('edit_item_description').value;
    const itemPrice = document.getElementById('edit_item_price').value;
    const itemQuantity = document.getElementById('edit_item_quantity').value;

    const data = new FormData();
    data.append('item_id', itemId);
    data.append('item_name', itemName);
    data.append('item_description', itemDescription);
    data.append('item_price', itemPrice);
    data.append('item_quantity', itemQuantity);

    // Make the AJAX request
    fetch('./database/edit-items.php', {
        method: 'POST',
        body: data
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message); // Show success message
            location.reload(); // Refresh the page
        } else {
            alert(data.message); // Show error message
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while editing the item. Please try again later.');
    });
}

</script>
</html>