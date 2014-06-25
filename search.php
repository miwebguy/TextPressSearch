<?php

class TextPressSearch
{
    
/**
 * @brief Output
 * Display field and results
 */
    public function displayfield()
    {
        $q = "";
        $list = "";
        if(isset($_REQUEST["q"])) {
            $q = $_REQUEST["q"];
            $results = $this->search_in_dir("articles",$q);
            $list = '<ul>';
            foreach($results as $post)
                $list .= "<li><a href=". $post["url"].">". $post['title'] ."</a></li>";
            $list .= '</ul>';
        }
        
        $html = <<<EOT
<form method=GET >
<input name=q size=15 maxlength=255 value="$q">
<input type=submit value="Search">
</form>
EOT;
        $html .= $list;
        return $html;
    }
    
/**
 * @brief Main Search Function
 * @return $results arr (array(title,url))
 */
    public function search_in_dir( $dir, $str )
    {
        $results = array();
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $filename)
            $files[] = $filename;

        foreach( $files as $k => $file )
        {
            $source = file_get_contents( $file );
            if( stripos( $source, $str ) !== false ) {
                $data=$this->extractDataFromSource($source);
                $results[]=$data;
                
            } else 
                unset( $files[$k] );
        }
        return array_filter($results);
    }

/**
 * @brief 
 * Get Url and Title from TextPress Article
 * @return arr(title=>,url=>)
 */
    public function extractDataFromSource($content)
    {
        $content    = str_replace("\r\n", "\n", $content);
        $content    = str_replace("\r", "\n", $content);
        $content    = preg_replace("/\n{2,}/", "\n\n", $content);
        $sections   = explode("\n\n", $content);
        
        $meta       = json_decode(array_shift($sections),true);
        $slug = (array_key_exists('slug', $meta) && $meta['slug'] !='') 
                    ? $meta['slug']
                    : $this->slugize($meta['title']);
  
        $article = array(
                    'title' => $meta['title'], 
                    'url'=>$this->getArticleUrl($meta['date'],$slug)
                ); 
                 
        return $article;
        
    }
/**
 * @brief Contruct Textpress Article Link
 * Could use Textpress class if it's available
 */
    public function getArticleUrl($date,$slug)
    {
        $date = new \DateTime($date);
        $date = $date->format('Y-m-d');
        $dateSplit = explode('-', $date);
        $url =  "/" . $dateSplit[0] .
                "/" . $dateSplit[1] .
                "/" . $dateSplit[2] .
                "/" . $slug;
        return $url;
                     
    }
    
/**
 *  @brief Create a Slug
 *  Could use Textpress class if it's available
 */
    public function slugize($str)
    {
        $str = strtolower(trim($str));
        
        $chars = array("ä", "ö", "ü", "ß");
        $replacements = array("ae", "oe", "ue", "ss");
        $str = str_replace($chars, $replacements, $str);

        $pattern = array("/(é|è|ë|ê)/", "/(ó|ò|ö|ô)/", "/(ú|ù|ü|û)/");
        $replacements = array("e", "o", "u");
        $str = preg_replace($pattern, $replacements, $str);

        $pattern = array(":", "!", "?", ".", "/", "'");
        $str = str_replace($pattern, "", $str);
        
        $pattern = array("/[^a-z0-9-]/", "/-+/");
        $str = preg_replace($pattern, "-", $str);
        
        return $str;
    }

}

$h= new TextPressSearch();

?>
  <h3>Search</h3>
  <?php echo $h->displayfield() ?>
