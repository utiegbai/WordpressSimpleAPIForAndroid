<?php
class Posts {
    private $Posts_id;
    private $_db;
    private $_prefix;

    public function AllPosts($post_type)
    {
        global $conn;
        $rows = array();
        
        $perpage = 10; 
        $page = 1; // Get the current page FROM $wp_query
        
        $counter = $perpage * $page;
        
        $uploadDir = 'hello/';
        // $uploadDir = $uploadDir['baseurl'];
        
        // INNER JOIN ctpostmeta wm2 ON (wm1.meta_value = wm2.post_id AND wm2.meta_key = '_wp_attached_file')
        
        $posts = [];
    

        $data = $conn->query("
            SELECT
                post.*,
                thumb.meta_value as thumbnail,
                post.post_type
            FROM (
                SELECT  p.*,
                    MAX(CASE WHEN pm.meta_key = '_thumbnail_id' then pm.meta_value ELSE NULL END) as thumbnail_id,
                  term.name as category_name,
                  term.slug as category_slug,
                  term.term_id as category_id
                FROM ctposts as p 
                LEFT JOIN ctpostmeta as pm ON ( pm.post_id = p.ID)
                LEFT JOIN ctterm_relationships as tr ON tr.object_id = p.ID
                LEFT JOIN ctterms as term ON tr.term_taxonomy_id = term.term_id
                WHERE p.post_status = 'publish' AND p.post_type = 'post'
                GROUP BY p.ID ORDER BY p.post_date DESC LIMIT 40
                ) as post
            LEFT JOIN ctpostmeta AS thumb 
            ON thumb.meta_key = '_wp_attached_file' AND thumb.post_id = post.thumbnail_id");
        if ($data->num_rows > 0) {
            while ($row = $data->fetch_assoc()) {
                $images = $this->getPostImages($row['ID']);
                
                $row['thumbnail'] = array_shift($images);
                $inf['posts'][] = $row;
                $inf['success'] = 1;
            }
        } else {
               $inf['posts'][] = 'No Posts';
               $inf['success'] = 0;
        }
        return $inf;
        // return $this->getPostMeta(40, 'cp_price');
    }

    public function searchPosts($search) {
        global $conn;
        $rows = array();
        
        $perpage = 10; 
        $page = 1; // Get the current page FROM $wp_query
        
        $counter = $perpage * $page;
        
        $uploadDir = 'hello/';
        // $uploadDir = $uploadDir['baseurl'];
        
        // INNER JOIN ctpostmeta wm2 ON (wm1.meta_value = wm2.post_id AND wm2.meta_key = '_wp_attached_file')
        
        $posts = [];
    

        $data = $conn->query("
            SELECT
                post.*,
                thumb.meta_value as thumbnail,
                post.post_type
            FROM (
                SELECT  p.*,
                    MAX(CASE WHEN pm.meta_key = '_thumbnail_id' then pm.meta_value ELSE NULL END) as thumbnail_id,
                  term.name as category_name,
                  term.slug as category_slug,
                  term.term_id as category_id
                FROM ctposts as p 
                LEFT JOIN ctpostmeta as pm ON ( pm.post_id = p.ID)
                LEFT JOIN ctterm_relationships as tr ON tr.object_id = p.ID
                LEFT JOIN ctterms as term ON tr.term_taxonomy_id = term.term_id
                WHERE p.post_status = 'publish' AND p.post_type = 'post' AND p.post_title LIKE '%$search%'
                GROUP BY p.ID ORDER BY p.post_date DESC
                ) as post
            LEFT JOIN ctpostmeta AS thumb 
            ON thumb.meta_key = '_wp_attached_file' AND thumb.post_id = post.thumbnail_id");
        if ($data->num_rows > 0) {
            while ($row = $data->fetch_assoc()) {
                $images = $this->getPostImages($row['ID']);
                $row['thumbnail'] = array_shift($images);
                $inf['posts'][] = $row;
                $inf['success'] = 1;
            }
        } else {
               $inf['posts'][] = 'No Posts';
               $inf['success'] = 0;
        }
        return $inf;
    }

    public function getCat() {
          global $conn;
          $rows = array();

        $data = $conn->query("SELECT t.term_id AS c_id, t.name  AS c_title, t.slug  AS c_url FROM  ctterms t LEFT JOIN ctterm_taxonomy tt ON t.term_id = tt.term_id WHERE  tt.taxonomy = 'category' ORDER  BY name");
        if ($data->num_rows > 0) {
            while ($row = $data->fetch_assoc()) {
                $inf['categories'][] = $row;
                $inf['success'] = 1;
            }
        } else {
               $inf['categories'][] = 'No Category';
               $inf['success'] = 0;
        }
        return $inf;
    }

    public function getCatPosts($cat_id) {
        global $conn;
        $rows = array();
        
        $perpage = 10; 
        $page = 1; // Get the current page FROM $wp_query
        
        $counter = $perpage * $page;
        
        $uploadDir = 'hello/';
        // $uploadDir = $uploadDir['baseurl'];
        
        // INNER JOIN ctpostmeta wm2 ON (wm1.meta_value = wm2.post_id AND wm2.meta_key = '_wp_attached_file')
        
        $posts = [];
        $data = $conn->query("
            SELECT
                post.*,
                thumb.meta_value as thumbnail,
                post.post_type
            FROM (
                SELECT  p.*,
                    MAX(CASE WHEN pm.meta_key = '_thumbnail_id' then pm.meta_value ELSE NULL END) as thumbnail_id,
                  term.name as category_name,
                  term.slug as category_slug,
                  term.term_id as category_id
                FROM ctposts as p 
                LEFT JOIN ctpostmeta as pm ON ( pm.post_id = p.ID)
                LEFT JOIN ctterm_relationships as tr ON tr.object_id = p.ID
                LEFT JOIN ctterms as term ON tr.term_taxonomy_id = term.term_id
                WHERE p.post_status = 'publish' AND p.post_type = 'post' AND term.term_id = '$cat_id'
                GROUP BY p.ID ORDER BY p.post_date DESC
                ) as post
            LEFT JOIN ctpostmeta AS thumb 
            ON thumb.meta_key = '_wp_attached_file' AND thumb.post_id = post.thumbnail_id");
        if ($data->num_rows > 0) {
            while ($row = $data->fetch_assoc()) {
                $images = $this->getPostImages($row['ID']);
                $row['thumbnail'] = array_shift($images);
                $inf['posts'][] = $row;
                $inf['success'] = 1;
            }
        } else {
               $inf['posts'][] = 'No Posts';
               $inf['success'] = 0;
        }
        return $inf;
        // return $this->getPostMeta(40, 'cp_price');
    }

    public function getSinglePost($post_id) {
        global $conn;
        $rows = array();
        $inf = [];
    
        $data = $conn->query("
            SELECT
                post.*,
                thumb.meta_value as thumbnail,
                post.post_type
            FROM (
                SELECT  p.*,
                    MAX(CASE WHEN pm.meta_key = '_thumbnail_id' then pm.meta_value ELSE NULL END) as thumbnail_id,
                  term.name as category_name,
                  term.slug as category_slug,
                  term.term_id as category_id
                FROM ctposts as p 
                LEFT JOIN ctpostmeta as pm ON ( pm.post_id = p.ID)
                LEFT JOIN ctterm_relationships as tr ON tr.object_id = p.ID
                LEFT JOIN ctterms as term ON tr.term_taxonomy_id = term.term_id
                WHERE p.post_status = 'publish' AND p.post_type = 'post' AND p.ID = {$post_id}
                GROUP BY p.ID ORDER BY p.post_date DESC
                ) as post
            LEFT JOIN ctpostmeta AS thumb 
            ON thumb.meta_key = '_wp_attached_file' AND thumb.post_id = post.thumbnail_id");
        if ($data->num_rows > 0) {
            while ($row = $data->fetch_assoc()) {
                $images = $this->getPostImages($row['ID']);
                
                $row['thumbnail'] = array_shift($images);
                $inf['posts'][] = array_merge($row, $this->getPostLatLong($row['ID']));
                $inf['success'] = 1;
            }
        } else {
               $inf['posts'][] = 'No Posts';
               $inf['success'] = 0;
        }
        
        return $inf;
    }

    public function getUserPost($user_id) {
        global $conn;
        $rows = array();
        
        $perpage = 10; 
        $page = 1; // Get the current page FROM $wp_query
        
        $counter = $perpage * $page;
        
        $uploadDir = 'hello/';
        // $uploadDir = $uploadDir['baseurl'];
        
        // INNER JOIN ctpostmeta wm2 ON (wm1.meta_value = wm2.post_id AND wm2.meta_key = '_wp_attached_file')
        
        $posts = [];
    

        $data = $conn->query("
            SELECT
                post.*,
                thumb.meta_value as thumbnail,
                post.post_type
            FROM (
                SELECT  p.*,
                    MAX(CASE WHEN pm.meta_key = '_thumbnail_id' then pm.meta_value ELSE NULL END) as thumbnail_id,
                  term.name as category_name,
                  term.slug as category_slug,
                  term.term_id as category_id
                FROM ctposts as p 
                LEFT JOIN ctpostmeta as pm ON ( pm.post_id = p.ID)
                LEFT JOIN ctterm_relationships as tr ON tr.object_id = p.ID
                LEFT JOIN ctterms as term ON tr.term_taxonomy_id = term.term_id
                WHERE p.post_status = 'publish' AND p.post_type = 'post' AND p.post_author = '{$user_id}'
                GROUP BY p.ID ORDER BY p.post_date DESC
                ) as post
            LEFT JOIN ctpostmeta AS thumb 
            ON thumb.meta_key = '_wp_attached_file' AND thumb.post_id = post.thumbnail_id");
        if ($data->num_rows > 0) {
            while ($row = $data->fetch_assoc()) {
                $images = $this->getPostImages($row['ID']);
                
                $row['thumbnail'] = array_shift($images);
                $inf['posts'][] = $row;
                $inf['success'] = 1;
            }
        } else {
               $inf['posts'][] = 'No Posts';
               $inf['success'] = 0;
        }
        return $inf;
        // return $this->getPostMeta(40, 'cp_price');
    }
    
    public function getPostImages($post_id, $limit = null) 
    {
          global $conn;
          $images = [];

        $data = $conn->query("
            SELECT * 
            FROM ctposts p1
            WHERE p1.post_parent = ".$post_id."
            AND p1.post_mime_type LIKE 'image%'");
        if ($data->num_rows > 0) {
            while ($attachments = $data->fetch_assoc()) {
                if (! empty($attachments)) {
                    //$post[] = $attachments;
                    
                    $images[] = $this->getPostAttachedFiles($attachments['ID']);
                }
            }
        }
        
        return $images;
    }

    public function registerUser($username, $email, $password, $fullName) {
      global $conn;
      //$fullName = $firstName." ".$lastName;
      $hashPassword = md5($password);
      $created = date("Y-m-d h:i:sa");
      $fName = urldecode($fullName);
      $data = $conn->query("INSERT INTO `ctusers` (`ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, `user_url`, `user_registered`, `user_activation_key`, `user_status`, `display_name`) VALUES ('', '{$username}','{$hashPassword}', '{$fName}', '{$email}', '', '{$created}', '', '1', '{$fName}')");
      if ($data) {
          $getInsertedId = $conn->insert_id;
          $wp_capabilities = ':1:{s:10:"subscriber";b:1;}';
          $conn->query("INSERT INTO `ctusermeta` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$getInsertedId}', 'wp_capabilities', '{$wp_capabilities}')");

          $conn->query("INSERT INTO `ctusermeta` (`user_id`, `meta_key`, `meta_value`) VALUES ('{$getInsertedId}', 'wp_user_level', '0')");
          $query = $conn->query("SELECT * FROM `ctusers` WHERE `ID` = '{$getInsertedId}' LIMIT 1");
          if ($query->num_rows > 0) {
            while ($row = $query->fetch_assoc()) {
                   $inf['user'][] = $row;
                   $inf['success'] = 1;
            }
          }
       } else {
         $inf['user'][] = "User Not registered";
         $inf['success'] = 0;
       }
       return $inf;
    }

    public function loginUser($username, $password) {
        global $conn;
        $hashPassword = md5($password);
        $query = $conn->query("SELECT * FROM `ctusers` WHERE `user_login` = '{$username}' AND `user_pass` = '{$hashPassword}' || `user_email` = '{$username}' AND `user_pass` = '{$hashPassword}' LIMIT 1");
          if ($query->num_rows > 0) {
            while ($row = $query->fetch_assoc()) {
                   $inf['user'][] = $row;
                   $inf['success'] = 1;
            }
          } else {
               $inf['user'][] = "Login Failed";
               $inf['success'] = 0;
          }
          return $inf;
    }
    
    public function getPostAttachedFiles($post_id) 
    {
          global $conn;
          $attached = null;

        $data = $conn->query("
            SELECT p1.meta_value 
            FROM ctpostmeta p1
            WHERE p1.post_id = ".$post_id."
            AND p1.meta_key = '_wp_attached_file'");
        if ($data->num_rows > 0) {
            
            while ($attachments = $data->fetch_assoc()) {
                if (! empty($attachments)) {
                    $attached = $attachments['meta_value'];
                    
                }
            }
        } 
        
        return $attached;
    }
    
    public function getPostMetaPrice($post_id)
    {
        global $conn;
        $meta = null;

        $data = $conn->query("SELECT p1.meta_value FROM ctpostmeta p1 WHERE p1.post_id = {$post_id} AND p1.meta_key = 'cp_price'");
        if ($data->num_rows > 0) {
            
            while ($results = $data->fetch_assoc()) {
                if (! empty($results)) {
                    $meta = $results['meta_value'];
                    
                }
            }
        } 
        
        return $meta;
    }

     public function getAuthorEmail($author_id)
    {
        global $conn;
        $user_email = null;

        $data = $conn->query("SELECT user_email FROM ctusers WHERE ID = {$author_id}");
        if ($data->num_rows > 0) {
            
            while ($results = $data->fetch_assoc()) {
                if (! empty($results)) {
                    $user_email = $results['user_email'];
                    
                }
            }
        } 
        
        return $user_email;
    }

    public function getPostLatLong($post_id)
    {
        global $conn;
        $meta = null;

        $data = $conn->query("SELECT lat,lng FROM ctcp_ad_geocodes WHERE post_id = {$post_id}");
        if ($data->num_rows > 0) {
            
            while ($results = $data->fetch_assoc()) {
                if (! empty($results)) {
                    $meta = $results;
                }
                else {
                    $meta = array("lat"=>"0.0","lng"=>"0.0");
                }
            }
        } else {
            $meta = array("lat"=>"0.0","lng"=>"0.0");
        }
        
        return $meta;
    }
    
    public function getPostMetaStreet($post_id)
    {
        global $conn;
        $meta = null;

        $data = $conn->query("SELECT p1.meta_value FROM ctpostmeta p1 WHERE p1.post_id = {$post_id} AND p1.meta_key = 'cp_street'");
        if ($data->num_rows > 0) {
            
            while ($results = $data->fetch_assoc()) {
                if (! empty($results)) {
                    $meta = $results['meta_value'];
                    
                }
            }
        } 
        
        return $meta;
    }
    
    public function getPostMetaCity($post_id)
    {
        global $conn;
        $meta = null;

        $data = $conn->query("SELECT p1.meta_value FROM ctpostmeta p1 WHERE p1.post_id = {$post_id} AND p1.meta_key = 'cp_city'");
        if ($data->num_rows > 0) {
            
            while ($results = $data->fetch_assoc()) {
                
                if (! empty($results)) {
                    $meta = $results['meta_value'];
                    
                }
            }
        } 
        
        return $meta;
    }
    
    public function getPostMetaState($post_id)
    {
        global $conn;
        $meta = null;

        $data = $conn->query("SELECT p1.meta_value FROM ctpostmeta p1 WHERE p1.post_id = {$post_id} AND p1.meta_key = 'cp_state'");
        if ($data->num_rows > 0) {
            
            while ($results = $data->fetch_assoc()) {
                
                if (! empty($results)) {
                    $meta = $results['meta_value'];
                    
                }
            }
        } 
        
        return $meta;
    }
    
    public function getPostMetaCountry($post_id)
    {
        global $conn;
        $meta = null;

        $data = $conn->query("SELECT p1.meta_value FROM ctpostmeta p1 WHERE p1.post_id = {$post_id} AND p1.meta_key = 'cp_country'");
        if ($data->num_rows > 0) {
            
            while ($results = $data->fetch_assoc()) {
                if (! empty($results)) {
                    $meta = $results['meta_value'];
                }
            }
        } 
        
        return $meta;
    }
    
    public function getPostMetaZipcode($post_id)
    {
        global $conn;
        $meta = null;

        $data = $conn->query("SELECT p1.meta_value FROM ctpostmeta p1 WHERE p1.post_id = {$post_id} AND p1.meta_key = 'cp_zipcode'");
        if ($data->num_rows > 0) {
            while ($results = $data->fetch_assoc()) {
                if (! empty($results)) {
                    $meta = $results['meta_value'];
                    
                }
            }
        } 
        
        return $meta;
    }
}
?>