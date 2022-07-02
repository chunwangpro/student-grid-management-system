<?php
namespace Think;
class Csv
{
    // csv导入 
    public function input_csv($csv_file) {
      
        $result_arr = array ();  
        $i = 0;  
        while($data_line = fgetcsv($csv_file,10000)) {
     //10000是表示可以处理多长的字符 
            if ($i == 0) {
      
                $GLOBALS ['csv_key_name_arr'] = $data_line;  
                $i ++;  
                continue;  
            }      
            foreach($GLOBALS['csv_key_name_arr'] as $csv_key_num => $csv_key_name ) {
      
                $result_arr[$i][$csv_key_name] = $data_line[$csv_key_num];  
            }  
            $i++;  
        }  
        return $result_arr;  
    }  
}
?>