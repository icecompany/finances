alter table `#__mkv_payments`
    add payerID int unsigned null default null,
    add constraint `#__mkv_payments_#__mkv_companies_payerID_id_fk` foreign key (payerID)
        references `#__mkv_companies` (id)
        on update cascade on delete restrict;

