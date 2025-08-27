<?php
include 'functions.php';
// Connect to MySQL database
$pdo = pdo_connect_mysql($db_host, $db_name, $db_user, $db_pass);
// Error message
$error_msg = '';
// Success message
$success_msg = '';
// Ensure contact "ID" param exists
if (!isset($_GET['id'])) {
    exit('No ID specified!');
}
// Get the contact from the contacts table
$stmt = $pdo->prepare('SELECT * FROM ' . $table . ' WHERE id = ?');
$stmt->execute([ $_GET['id'] ]);
$contact = $stmt->fetch(PDO::FETCH_ASSOC);
// Contact doesn't exist with the specified ID, so output error message and stop the script
if (!$contact) {
    exit('Contact doesn\'t exist with that ID!');
}
// Check if POST data exists (user submitted the form)
if (isset($_POST['submit'])) {
    // Iterate through the fields and extract the data from the form
    $data = [];
    foreach ($columns as $column => $array) {
        if (isset($_POST[$column])) {
            $data[$column] = $_POST[$column];
            // Validate
            if ((isset($array['input']['required']) && !$array['input']['required'] && empty($_POST[$column]))) {
                continue;
            }
            if (isset($array['input']['validate_regex']) && $array['input']['validate_regex']) {
                if (!preg_match($array['input']['validate_regex'], $_POST[$column])) {
                    $error_msg = isset($array['input']['validate_msg']) ? $array['input']['validate_msg'] : 'Please enter a valid ' . $column . '.';
                }
            }
        }
    }
    // If no validation errors, proceed to update the record(s) in the database
    if (!$error_msg) {
        // Update the record
        $stmt = $pdo->prepare('UPDATE ' . $table . ' SET ' . implode(', ', array_map(function ($column) {
            return $column . ' = :' . $column;
        }, array_keys($data))) . ' WHERE id = :id');
        // bind over the data to the placeholders in the prepared statement
        foreach ($data as $column => $value) {
            $stmt->bindValue(':' . $column, $value);
        }
        // Bind ID
        $stmt->bindValue(':id', $_GET['id']);
        // Execute the SQL statement
        $stmt->execute();
        // Get the updated contact from the contacts table
        $stmt = $pdo->prepare('SELECT * FROM  ' . $table . ' WHERE id = ?');
        $stmt->execute([ $_GET['id'] ]);
        $contact = $stmt->fetch(PDO::FETCH_ASSOC);
        // Output message
        $success_msg = 'Updated Successfully!';
    }
}
?>
<?=template_header('Update')?>

<div class="content update">

    <div class="page-title">
		<i class="fa-solid fa-user fa-lg"></i>
		<div class="wrap">
			<h2>Update Contact #<?=$contact['id']?></h2>
			<p>Update the contact #<?=$contact['id']?> details in the database, fill in the form below and submit.</p>
		</div>
	</div>

    <form action="?id=<?=$contact['id']?>" method="post" class="crud-form">

        <div class="cols">
            <?php foreach ($columns as $column => $array): ?>
            <?php if (isset($array['input'])): ?>
            <?php $input = $array['input']; ?>
            <div class="form-control">
                <label for="<?=$column?>"><?=$array['label']?></label>
                <?php if ($input['type'] == 'text' || $input['type'] == 'hidden' || $input['type'] == 'email' || $input['type'] == 'number' || $input['type'] == 'tel'): ?>
                <input id="<?=$column?>" type="<?=$input['type']?>" name="<?=$column?>" placeholder="<?=$input['placeholder']?>" value="<?=htmlspecialchars($contact[$column], ENT_QUOTES)?>" <?=$input['required'] ? 'required' : ''?> <?=isset($input['custom']) ? $input['custom'] : ''?>>
                <?php elseif ($input['type'] == 'datetime-local'): ?>
                <input id="<?=$column?>" type="<?=$input['type']?>" name="<?=$column?>" value="<?=date('Y-m-d\TH:i', strtotime($contact[$column]))?>" <?=$input['required'] ? 'required' : ''?> <?=isset($input['custom']) ? $input['custom'] : ''?>>
                <?php elseif ($input['type'] == 'textarea'): ?>
                <textarea id="<?=$column?>" name="<?=$column?>" placeholder="<?=$input['placeholder']?>" <?=$input['required'] ? 'required' : ''?> <?=isset($input['custom']) ? $input['custom'] : ''?>><?=htmlspecialchars($contact[$column], ENT_QUOTES)?></textarea>
                <?php elseif ($input['type'] == 'select'): ?>
                <select id="<?=$column?>" name="<?=$column?>" <?=$input['required'] ? 'required' : ''?> <?=isset($input['custom']) ? $input['custom'] : ''?>>
                    <?php foreach ($input['options'] as $option): ?>
                    <option value="<?=$option?>" <?=$contact[$column] == $option ? 'selected' : ''?>><?=$option?></option>
                    <?php endforeach; ?>
                </select>
                <?php elseif ($input['type'] == 'checkbox'): ?>
                <input id="<?=$column?>" type="hidden" name="<?=$column?>" value="0" <?=isset($input['custom']) ? $input['custom'] : ''?>>
                <input type="<?=$input['type']?>" name="<?=$column?>" value="1" <?=$contact[$column] == 1 ? 'checked' : ''?> <?=isset($input['custom']) ? $input['custom'] : ''?>>
                <?php elseif ($input['type'] == 'radio'): ?>
                <?php foreach ($input['options'] as $option): ?>
                <div>
                    <input id="<?=$option?>" type="<?=$input['type']?>" name="<?=$column?>" value="<?=$option?>" <?=$contact[$column] == $option ? 'checked' : ''?> <?=isset($input['custom']) ? $input['custom'] : ''?>>
                    <label for="<?=$option?>"><?=$option?></label>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <?php if ($error_msg): ?>
        <p class="msg-error"><?=$error_msg?></p>
        <?php endif; ?>

        <?php if ($success_msg): ?>
        <p class="msg-success"><?=$success_msg?></p>
        <?php endif; ?>

        <button type="submit" name="submit" class="btn">Save Record</button>

    </form>

</div>

<?=template_footer()?>