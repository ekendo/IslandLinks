#############################################
# SQL update script for upgrading 
# from phpshop package 1.2 stable (-pl2) to 1.2 stable-pl3
#
#############################################

# 09.06.2005
INSERT INTO `mos_pshop_csv` VALUES
  ('', 'attributes', '', 24, 'N' ),  ('', 'attribute_values', '', 25, 'N' );