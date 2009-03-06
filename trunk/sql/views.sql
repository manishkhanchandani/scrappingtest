create view totalpoi AS select count(province) as cnt, province FROM us_xml_yahoo WHERE hotel_id != 0 GROUP BY province;

create view totalpoicompleted AS select count(province) as cnt, province from us_xml_yahoo WHERE flag = 1 AND hotel_id != 0 GROUP BY province;

create view totalpoinotcompleted AS select count(province) as cnt, province from us_xml_yahoo WHERE flag = 0 AND hotel_id != 0 GROUP BY province;

create view totalpoifound AS select count(province) as cnt, province from us_xml_yahoo WHERE gotpoi = 1 AND hotel_id != 0 GROUP BY province;

create view hotelidcheck as select count(hotel_id) as cnt, province from us_xml_yahoo WHERE hotel_id != 0 GROUP BY province;

