#! /bin/bash

mysql -v "$@" <<EOF
  connect mysql;
  delete from user where user='wam';
  insert into user (host,user,password,select_priv,insert_priv,update_priv,delete_priv,create_priv,alter_priv)
    values ('localhost','wam','','y','y','y','y','y','y');

  create database wam;
  connect wam;
  
  create table tblConfiguration (
    ConfigKey text,
    ConfigValue text
    );
    
  insert into tblConfiguration values ('DBVersion','2');
  
  create table tblBillingEntry (
    BillingEntryID int default 0 auto_increment primary key,
    RecipientID int not null default 0,
    Text text,
    StoreID int not null default 0,
    Entered timestamp,
    Due date not null,
    Quantity double,
    Amount double,
    Tax double default 0,
    Done tinyint
    );

  create table tblBillingIOU (
    BillingIOUID int default 0 auto_increment primary key,
    BillingEntryID int not null default 0,
    DebtorID int not null default 0,
    Amount double,
    Paid tinyint not null default 0,
    PaidTime timestamp
    );

  create table tblChat (
    MessageID int default 0 auto_increment primary key,
    Person int not null default 0,
    Text text
    );

  create table tblConnect (
    ConnectID int default 0 auto_increment primary key,
    PersonID int not null default 0,
    Start datetime,
    End datetime,
    Done tinyint
    );

  create table tblPerson (
    PersonID int default 0 auto_increment primary key,
    Name text,
    FirstName text,
    Phone text,
    email text,
    Login varchar(50) unique not null,
    Password text,
    Handle text,
    Account text,
    Bank text,
    BLZ text,
    Inactive tinyint
    );

  create table tblPhoneTariff (
    PhoneTariffID int default 0 auto_increment primary key,
    Start time,
    End time,
    StartWDay int,
    EndWDay int,
    Seconds double,
    Cost double
    );

  create table tblService (
    ServiceID varchar(50) primary key,
    ServiceName text,
    IconPath text
    );

  create table tblServiceRules (
    RuleID int default 0 auto_increment primary key,
    PersonID int not null default 0,
    ServiceID varchar(50) not null,
    Rule text,
    Comment text
    );

  create table tblShoppingItem (
    ItemID int default 0 auto_increment primary key,
    StoreID int not null default 0,
    Qty double,
    Unit text,
    Name text,
    Permanent tinyint,
    Refill tinyint
    );

  create table tblStore (
    StoreID int default 0 auto_increment primary key,
    Store text,
    Location text
    );
    
EOF
