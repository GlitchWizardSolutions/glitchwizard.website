<?php
include 'main.php';
// Default client values
$client = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'phone' => '',
    'address_street' => '',
    'address_city' => '',
    'address_state' => '',
    'address_zip' => '',
    'address_country' => '',
    'created' => date('Y-m-d\TH:i')
];
// Check if the ID param exists
if (isset($_GET['id'])) {
    // Retrieve the client from the database
    $stmt = $pdo->prepare('SELECT * FROM invoice_clients WHERE id = ?');
    $stmt->execute([ $_GET['id'] ]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    // ID param exists, edit an existing client
    $page = 'Edit';
    if (isset($_POST['submit'])) {
        // Update the client
        $stmt = $pdo->prepare('UPDATE invoice_clients SET first_name = ?, last_name = ?,  email = ?, phone = ?, address_street = ?, address_city = ?, address_state = ?, address_zip = ?, address_country = ?, created = ? WHERE id = ?');
        $stmt->execute([ $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'], $_POST['address_street'], $_POST['address_city'], $_POST['address_state'], $_POST['address_zip'], $_POST['address_country'], $_POST['created'], $_GET['id'] ]);
        header('Location: clients.php?success_msg=2');
        exit;
    }
    if (isset($_POST['delete'])) {
        // Redirect and delete client
        header('Location: clients.php?delete=' . $_GET['id']);
        exit;
    }
} else {
    // Create a new client
    $page = 'Create';
    if (isset($_POST['submit'])) {
        $stmt = $pdo->prepare('INSERT INTO invoice_clients (first_name,last_name,email,phone,address_street,address_city,address_state,address_zip,address_country,created) VALUES (?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([ $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'], $_POST['address_street'], $_POST['address_city'], $_POST['address_state'], $_POST['address_zip'], $_POST['address_country'], $_POST['created'] ]);
        header('Location: clients.php?success_msg=1');
        exit;
    }
}
?>
<?=template_admin_header($page . ' Client', 'invoices', 'clients_manage')?>

<div class="content-title mb-4">
    <div class="title">
        <div class="icon">
            <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z" /></svg>
        </div>
        <div class="txt">
            <h2><?=$page?> Client</h2>
            <p>Manage client information and contact details.</p>
        </div>
    </div>
</div>

<div style="height: 20px;"></div>

<div class="d-flex gap-2 mb-4">
    <a href="clients.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Cancel
    </a>
    <button type="submit" name="submit" form="client-form" class="btn btn-success">
        <i class="bi bi-save me-1"></i>Save Client
    </button>
    <?php if ($page == 'Edit'): ?>
    <button type="submit" name="delete" form="client-form" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this client?')">
        <i class="bi bi-trash me-1"></i>Delete
    </button>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="card-title mb-0"><?=$page?> Client</h6>
        <small class="text-muted">Complete the form below to manage client information</small>
    </div>
    <div class="card-body">
        <form action="" method="post" id="client-form">
            <?php if (isset($error_msg)): ?>
            <div class="alert alert-danger" role="alert">
                <i class="bi bi-exclamation-octagon-fill me-2"></i>
                <?=$error_msg?>
            </div>
            <?php endif; ?>

            <!-- Primary Information -->
            <fieldset class="mb-4">
                <legend class="h6 text-primary border-bottom pb-1">Primary Information</legend>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?=htmlspecialchars($client['email'], ENT_QUOTES)?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="created" class="form-label">Created <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="created" name="created" value="<?=date('Y-m-d\TH:i', strtotime($client['created']))?>" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" value="<?=htmlspecialchars($client['first_name'], ENT_QUOTES)?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" value="<?=htmlspecialchars($client['last_name'], ENT_QUOTES)?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone" value="<?=htmlspecialchars($client['phone'], ENT_QUOTES)?>">
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- Address Information -->
            <fieldset class="mb-4">
                <legend class="h6 text-primary border-bottom pb-1">Address Information</legend>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="address_street" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address_street" name="address_street" placeholder="Street" value="<?=htmlspecialchars($client['address_street'], ENT_QUOTES)?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="address_city" class="form-label">City</label>
                            <input type="text" class="form-control" id="address_city" name="address_city" placeholder="City" value="<?=htmlspecialchars($client['address_city'], ENT_QUOTES)?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="address_state" class="form-label">State</label>
                            <input type="text" class="form-control" id="address_state" name="address_state" placeholder="State" value="<?=htmlspecialchars($client['address_state'], ENT_QUOTES)?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="address_zip" class="form-label">Zip</label>
                            <input type="text" class="form-control" id="address_zip" name="address_zip" placeholder="Zip" value="<?=htmlspecialchars($client['address_zip'], ENT_QUOTES)?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="address_country" class="form-label">Country</label>
                            <select class="form-select" id="address_country" name="address_country">
                                <?php foreach(get_countries() as $country): ?>
                                <?php 
                                // Default to United States if no country is set (new client)
                                $selected = '';
                                if ($client['address_country'] == $country) {
                                    $selected = ' selected';
                                } elseif (empty($client['address_country']) && $country == 'United States') {
                                    $selected = ' selected';
                                }
                                ?>
                                <option value="<?=$country?>"<?=$selected?>><?=$country?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>

<?=template_admin_footer()?>