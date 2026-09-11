USE portfolio_builder;
ALTER TABLE templates ADD COLUMN preview_data LONGTEXT NULL, ADD COLUMN template_html LONGTEXT NULL, ADD COLUMN template_css LONGTEXT NULL, ADD COLUMN fields_json LONGTEXT NULL;
ALTER TABLE portfolios ADD COLUMN custom_data LONGTEXT NULL;