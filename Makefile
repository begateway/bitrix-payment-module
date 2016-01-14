all:
	if [[ -e bitrix-begateway.zip ]]; then rm bitrix-begateway.zip; fi
	 zip -r bitrix-begateway.zip devtm.begateway
