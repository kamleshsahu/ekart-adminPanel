<?php
include_once('includes/functions.php');
$function = new functions;
include_once('includes/custom-functions.php');
$fn = new custom_functions;

?>

<?php
if (isset($_POST['btnAdd'])) {
    if($permissions['products']['create']==1){

    $title = $db->escapeString($fn->xss_clean($_POST['title']));
    $percentage = $db->escapeString($fn->xss_clean($_POST['percentage']));

    // create array variable to handle error
    $error = array();

    if (empty($title)) {
        $error['title'] = " <span class='label label-danger'>Required!</span>";
    }
    if ($percentage == '') {
        $error['percentage'] = " <span class='label label-danger'>Required!</span>";
    }

    if (!empty($title) && $percentage != '') {

        $sql_query = "INSERT INTO taxes (`title`,`percentage`, `status`) VALUES ('$title', $percentage, 1)";
        // Execute query
        $db->sql($sql_query);
        // store result 
        $result = $db->getResult();
        if (!empty($result)) {
            $result = 0;
            // unset($_POST['btnAdd']);
        } else {
            $result = 1;
            // unset($_POST['btnAdd']);
        }

        if ($result == 1) {
            $error['add_taxes'] = " <section class='content-header'><span class='label label-success'>Tax Added Successfully</span></section>";
            // $_POST = array();
             
            
        } else {
            $error['add_taxes'] = " <span class='label label-danger'>Failed add tax</span>";
            // $_POST = array();
        }
    }
    }else{
    	$error['check_permission'] = " <section class='content-header'>
    										<span class='label label-danger'>You have no permission to create tax</span>
    										</section>";
    }
}
?>

<section class="content-header">
    <h1>Add Taxes <small><a href='products-taxes.php'> <i class='fa fa-angle-double-left'></i>&nbsp;&nbsp;&nbsp;Back to Taxes</a></small></h1>

    <?php echo isset($error['add_taxes']) ? $error['add_taxes'] : ''; ?>
    <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
    </ol>
    <hr />
</section>
<section class="content">
    <div class="row">
        <div class="col-md-6">
            <?php if($permissions['products']['create']==0) { 
                    ?>
        	<div class="alert alert-danger">You have no permission to create tax.</div>
        <?php } 
        ?>
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Add Taxes</h3>

                </div><!-- /.box-header -->
                <!-- form start -->
                <form method="post" id="tax_form">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="title">Title</label><?php echo isset($error['title']) ? $error['title'] : ''; ?>
                            <input type="text" class="form-control" name="title" placeholder="Title Of Tax" required>
                        </div>
                        <div class="form-group">
                            <label for="percentage">Percentage</label><?php echo isset($error['percentage']) ? $error['percentage'] : ''; ?>
                            <input type="text" class="form-control" name="percentage" placeholder="10.00" required min=0>
                        </div>
                    </div><!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" name="btnAdd">Add</button>
                        <input type="reset" class="btn-warning btn" value="Clear" />

                    </div>

                </form>

            </div><!-- /.box -->
            <?php echo isset($error['check_permission']) ? $error['check_permission'] : '';?>
        </div>
    </div>
</section>

<div class="separator"> </div>

<?php $db->disconnect(); ?>