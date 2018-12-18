CREATE DATABASE IF NOT EXISTS integrator
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS integrator.players
(
  id    INT(11)     NOT NULL AUTO_INCREMENT,
  name  VARCHAR(20) NOT NULL,
  email VARCHAR(20) NOT NULL,
  PRIMARY KEY (id)
)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS integrator.balances
(
  id        INT(11)        NOT NULL AUTO_INCREMENT,
  player_id INT(11)        NOT NULL,
  amount    DECIMAL(15, 2) NOT NULL DEFAULT '0.00',
  currency  VARCHAR(4)     NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (player_id)
  REFERENCES integrator.players (id)
)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS integrator.sessions
(
  id          INT(11)        NOT NULL AUTO_INCREMENT,
  player_id   INT(11)        NOT NULL,
  begin_time  DATETIME,
  end_time    DATETIME,
  total_bet   DECIMAL(15, 2) NOT NULL DEFAULT '0.00',
  total_win   DECIMAL(15, 2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (id),
  FOREIGN KEY (player_id)
  REFERENCES integrator.players (id)
)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS integrator.transactions
(
  id                 INT(11)        NOT NULL AUTO_INCREMENT,
  player_id          INT(11)        NOT NULL,
  balance_id         INT(11)        NOT NULL,
  game_uuid          VARCHAR(255)   NOT NULL,
  session_id         INT(11)        NOT NULL,
  transaction_id     VARCHAR(255)   NOT NULL,
  action             VARCHAR(20)    NOT NULL,
  amount             DECIMAL(15, 2) NOT NULL,
  currency           VARCHAR(4)     NOT NULL,
  type               VARCHAR(20),
  bet_transaction_id VARCHAR(255),
  success            TINYINT(1)     NOT NULL
  COMMENT '0 - no, 1 - yes',
  PRIMARY KEY (id),
  FOREIGN KEY (player_id)
  REFERENCES integrator.players (id),
  FOREIGN KEY (balance_id)
  REFERENCES integrator.balances (id),
  FOREIGN KEY (session_id)
  REFERENCES integrator.sessions (id)
)
  ENGINE = InnoDB;
