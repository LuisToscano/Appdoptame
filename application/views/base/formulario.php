<?php

// viene de la funcion crea_form del modelo m_data

//var_dump($form);

if(is_array($form) or is_object($form)){

$id_unico = $form['id_unico'];


unset($form['id_unico']);
if (!isset($table_striped)) {
    $table_striped = TRUE;
}
?>

<table id="<?php echo $id_unico; ?>" width="100%" class=" <?php if ($table_striped !== FALSE) { ?>table table-striped<?php } ?>">
    <?php
    
    if (isset($form['extra'])) {
        $extra = $form['extra'];
        $extra2 = $form['extra2'];
        unset($form['extra2']);
        unset($form['extra']);
    }
    foreach ($form as $llave => $valor_array) {
        echo '<tr>';
        if (!is_array($valor_array)) {
            echo '<td colspan="2">' . $valor_array . '</td>';
        } else {
            foreach ($valor_array as $llave2 => $valor_array2) {
                if (!is_array($valor_array2)) {
                    echo '<td>' . $valor_array2 . '</td>';
                } else {
                    echo '<td>';
                    foreach ($valor_array2 as $llave3 => $valor_array3) {
                        if (!is_array($valor_array3)) {
                            echo $valor_array3;
                        } else {
                            print_r($valor_array3);
                        }
                    }
                    echo '</td>';
                }
            }
        }
        echo '</tr>';
    }
    ?>
</table>

<script>
    $('document').ready(function() {
<?php echo $extra2; ?>
    });
</script>

<script>
    $('document').ready(function() {
        $.validator.addMethod(
                "normalDate",
                function(value, element) {
                    // put your own logic here, this is just a (crappy) example
                    return value.match(/^\d\d?\/\d\d?\/\d\d\d\d$/);
                },
                "Please enter a date in the format dd/mm/yyyy."
                );

        $('#<?php echo $id_unico ?>').parents('form:eq(0)').validate({
            debug: false,
            rules: {
<?php echo $extra; ?>
            },
            highlight: function(element) {
                $(element).parents('tr:eq(0)').addClass('has-error');
                $(element).addClass('error');
            },
            unhighlight: function(element) {
                $(element).parents('tr:eq(0)').removeClass('has-error');
                $(element).removeClass('error');
            },
            errorPlacement: function(error, element) {

            }
        });
    });

</script>
<?php
}else{
    var_dump($form);
}
?>