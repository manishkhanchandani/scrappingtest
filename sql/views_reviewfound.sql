drop VIEW `reviewfound`;
create VIEW `reviewfound` AS select count(`province`) AS `cnt`,`province` AS `province` from `us_xml_yahoo` where `reviewfound` > 0 and hotel_id != 0 group by `province`;