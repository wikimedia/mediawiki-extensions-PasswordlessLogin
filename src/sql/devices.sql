--
-- extension PasswordlessLogin
--
CREATE TABLE /*$wgDBprefix*/passwordlesslogin_devices (
  id int NOT NULL AUTO_INCREMENT,
  device_id varchar(255) NULL,
  device_user_id int(10) unsigned NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY (device_id)
) /*$wgDBTableOptions*/;
