<?php

function display_test($msg, $color='red', $size='20px'){
    return "<h1 style='color:$color; font-size: $size;'>$msg</h1>";
}

echo display_test("HAPPY", 'darkgreen','55px');
echo display_test('Holidays!!!','darkred','40px');
echo display_test('From wftutorials :)','black','20px');