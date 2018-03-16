Drop table WaterResponses;

Create table WaterResponses (
  id            varchar(32) primary key, -- stores an MD5 hash rather than an ID
  rlc           varchar(64),
  showerTime    int,
  freq          int,
  showerLoc     varchar(10),
  shavelegs     varchar(3),
  shaveface     varchar(3),
  faucet        varchar(3),
  wash          int,
  drink         int,
  flush         int,
  laundry       int,
  dishwasher    int,
  handwash      int,
  showerTotal   number,
  brushTotal    number,
  flushTotal    number,
  laundryTotal  number,
  dishesTotal   number,
  total         number
);

-- hash('ripemd160', 'userid');
