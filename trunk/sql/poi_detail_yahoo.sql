CREATE TABLE `poi_detail_yahoo` (            
                    `id` int(11) NOT NULL auto_increment,      
                    `poi_id` int(11) NOT NULL,                 
                    `poi_name` varchar(255) default NULL,      
                    `reviewer` varchar(255) default NULL,      
                    `reviewdate` varchar(50) default NULL,     
                    `review_title` varchar(100) default NULL,  
                    `rating` varchar(20) default NULL,         
                    `review_detail` text,                      
                    `source` text,                             
                    `filename` text,                           
                    `targetSite` varchar(255) default NULL,    
                    `avgrating` varchar(20) default NULL,      
                    `xml_id` int(11) default NULL,             
                    `province` varchar(50) default NULL,       
                    PRIMARY KEY  (`id`)                        
                  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;