<?php
$data = isset( $data ) && is_array( $data ) ? $data : []; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
?>
<!doctype html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html charset=UTF-8"/>
</head>
<body>
<table cellpadding="10" cellspacing="10" border="0">
	<tr>
		<td>Dear <b>Administrator</b>,</td>
	</tr>
	<tr>
		<td>
			Content Delivery Network.<br/>
			You have reached your <?php echo esc_html( $data['limit'] ); ?> limit. Please upgrade your subscription. <a href="<?php echo esc_html( $data['site_url'] ); ?>"><?php echo esc_html( $data['site_url'] ); ?></a>
		</td>
	</tr>
	<tr>
		<td><a href="<?php echo esc_html( $data['panel_link'] ); ?>" style="text-decoration: none; padding: 10px 20px; background: #2B98F0; width: 75px; height: 16px; font-style: normal; font-weight: 400; font-size: 14px; color: #FFFFFF;">Upgrade subscription</a></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>
			<table cellpadding="3" cellspacing="0" border="0">
				<td><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB8AAAAfCAYAAAAfrhY5AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAK5SURBVHgBrVc9mtMwEH1AAxUu6VBJtz6CtqN0S4VuQG4Qc4LNDeySLuwJwg0CHZ1T0nlLttrVxDPRSLbj2Nn3fe+zLM2vNJYlYBlWnjvPJ2brufV0mIFXF8oZT6veD0xw/5plZOy7Z40rkLHRBiHDlDsV1CoZu1MBzUI+4TTlmvVMokfBbTEjiK+JMmVUoMvQcrseCG41EkDL72bKccHCJbppN+wwG5F3ylGr5Cz6s7M/Y+foqOanQ1zNouwSHYs4Uz1eK10ZL3EGhp2cW98Gocgk0Iz1fiaB6eVLZ6fnuEW/mjfoliNnGn5qvW9sdMt94qBVdiR7qQ28UQZI4AO///b8gu57/cv9JPPJ87OSITx4vvN863nP7yT3n4Ok9h/Pf9x+9Pyhgkelst3gelh2XCPUgrQbEXqNsG4EynSlDNBGsUM8zXMCIBzQ1cL7IaFNEpFFKBAprhXmOybWCDMpa75XMqfqNuh2qqcrHRMKfm4QClTsyhdBCZ6qUQvIOjm8DKrErvg6dehNpcKydR6CQ5yUQfgJnTpSAY0My7BO7Jbc3zCPG4NFnLVWtpjYFkdAG4/etKR2nPZjEU+NZSHD7zcLnFfsmEhFlyubUvVSlIPOpUg+Yn7hOcQnn9RxowcK5VwiEsFc9S1FejhxejBDvDZ5ImiUkXLAeMH9JcvfKPmxoouwQ/yZDRUgydgBA3csJ9xi+BhWYgQW4Z9rEB+DBCUbLRBPHcnX6P+S9W5JOhnOfLaSfYF403FJgMQ1hvcDkqUKp+wr5dSyzVFkHOU+cdQqR7skIzdhTy4YDZJghy4NhiO+Rzi7Ew6etyoAbeiA+CJBejnii8StGp+EY2X55epMqb/G+bOezFiJ5dvzESX6ZzuL8JOQaRVWCAfLF4FBP1txVGDB1ejSi6KGrGfO7Qd0a/mL2xfjGQ6vSGdqSs2ZAAAAAElFTkSuQmCC" alt="Logo"></td>
				<td style="vertical-align: top; font-size: 12px;">Regards,<br/> <a style="color: #0085FF" href='https://www.cloudlinux.com/'>CloudLinux</a></td>
			</table>
		</td>
	</tr>
</table>
</body>
</html>
