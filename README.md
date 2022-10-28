# PHP LDAP query form

Quickly search entries of an LDAP directory (w/o authentication).

## Setup
### Server
Requires PHP with enabled *ldap* extension.

### Config
LDAP server settings in *config.php*:

`server` = server url (domain controller)  
`port` = LDAP port (usually 389)  
`base_dn` = the directory/group to search in
