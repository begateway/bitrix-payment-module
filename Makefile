all:
	if [[ -e bitrix-begateway.zip ]]; then rm bitrix-begateway.zip; fi
	if [[ -e bitrix-begateway-windows-1251.zip ]]; then rm bitrix-begateway-windows-1251.zip; fi
	zip -r bitrix-begateway.zip begateway.payment
	find begateway.payment -name \*.php -exec sh -c 'iconv -f utf-8 -t cp1251 {} > {}.1251 && mv {}.1251 {}' \;
	zip -r bitrix-begateway-windows-1251.zip begateway.payment
	git checkout -f begateway.payment
