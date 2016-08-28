all:
	if [[ -e bitrix-begateway.zip ]]; then rm bitrix-begateway.zip; fi
	if [[ -e bitrix-begateway-windows-1251.zip ]]; then rm bitrix-begateway-windows-1251.zip; fi
	zip -r bitrix-begateway.zip begateway.payment -x begateway.payment/lib/beGateway/test/*
	find begateway.payment -name \*.php -exec sh -c 'iconv -f utf-8 -t cp1251 {} > {}.1251 && mv {}.1251 {}' \;
	zip -r bitrix-begateway-windows-1251.zip begateway.payment -x begateway.payment/lib/beGateway/test/*
	git checkout -f begateway.payment
	cd begateway.payment/lib/beGateway && git checkout -f
	find begateway.payment -name \*.php.1251 -exec sh -c 'rm -f {}' \;
