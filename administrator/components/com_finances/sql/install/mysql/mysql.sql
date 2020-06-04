create table `#__mkv_scores`
(
    id         int unsigned   not null auto_increment primary key,
    dat        date           not null,
    contractID int unsigned   not null,
    number     varchar(255)   not null,
    amount     decimal(11, 2) not null default 0,
    payments   decimal(11, 2) not null default 0,
    debt       decimal(11, 2) not null default 0,
    status     tinyint        not null default 0,
    index `#__mkv_scores_status_index` (status),
    constraint `#__mkv_scores_#__mkv_contracts_contractID_id_fk` foreign key (contractID)
        references `#__mkv_contracts` (id)
        on update cascade on delete restrict
) character set utf8
  collate utf8_general_ci;

create table `#__mkv_payments`
(
    id         int unsigned   not null auto_increment primary key,
    dat        date           not null,
    scoreID    int unsigned   not null,
    order_name varchar(255)   null     default null,
    amount     decimal(11, 2) not null default 0,
    constraint `#__mkv_payments_#__mkv_scores_scoreID_id_fk` foreign key (scoreID)
        references `#__mkv_scores` (id)
        on update cascade on delete cascade
) character set utf8
  collate utf8_general_ci;

insert into `#__mkv_scores`
select id,
       dat,
       contractID,
       number,
       amount,
       0,
       0,
       state
from `#__prj_scores`;

insert into `#__mkv_payments`
select id, dat, scoreID, if(pp = 0, null, pp), amount
from `#__prj_payments`;

alter table `#__mkv_contracts`
    add payments decimal(11, 2) not null default 0 after amount,
    add debt     decimal(11, 2) not null default 0 after payments,
    add index `#__mkv_contracts_payments_index` (payments),
    add index `#__mkv_contracts_debt_index` (debt);

update `#__mkv_scores` s
set s.payments = (
    select ifnull(sum(p.amount), 0)
    from `#__mkv_payments` p
    where p.scoreID = s.id
);

update `#__mkv_scores`
set debt = ifnull(ifnull(amount, 0) - ifnull(payments, 0), 0);
update `#__mkv_scores` set status = if(debt = 0, 1, if(amount = debt, 0, 2));


update `#__mkv_contracts` c
set c.payments = (
    select ifnull(sum(p.amount), 0)
    from `#__mkv_payments` p
             left join `#__mkv_scores` s on p.scoreID = s.id
    where s.contractID = c.id
);

update `#__mkv_contracts`
set debt = ifnull(ifnull(amount, 0) - ifnull(payments, 0), 0);
