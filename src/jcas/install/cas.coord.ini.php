;<?php die(''); ?>
;for security reasons , don't remove or modify the first line

driver = cas

;============ Parameters for the plugin
; session variable name
session_name = "JELIX_USER"

; set to 'on' to destroy the session on logout
session_destroy = 

; If the value is "on", the user must be authentificated for all actions, except those
; for which a plugin parameter  auth.required is false
; If the value is "off", the authentification is not required for all actions, except those
; for which a plugin parameter  auth.required is true
auth_required = on

; What to do if an authentification is required but the user is not authentificated
; 1 = generate an error. This value should be set for web services (xmlrpc, jsonrpc...)
; 2 = redirect to an action
on_error = 2

; locale key for the error message when on_error=1
error_message = "jcas~autherror.notlogged"

; action to execute on a missing authentification when on_error=2
on_error_action = "cas~default:notauthenticated"


;------- parameters for the "cas" driver
[cas]

; server version. CAS_1.0, CAS_2.0, SAML_1.1
server_version= "SAML_1.1"

; name of the dao to get user data
dao = "jauthdb~jelixuser"

; profile to use for jDb 
profile = ""


; name of the form for the jauthdb_admin module
form = ""

; path of the directory where to store files uploaded by the form (jauthdb_admin module)
; should be related to the var directory of the application
uploadsDirectory= ""


# Full Hostname of your CAS Server
host = cas.example.com

# Context of the CAS Server
context = "/cas"

# Port of your CAS server. Normally for a https server it's 443
port = 443;

# Path to the ca chain that issued the cas server certificate
server_ca_cert_path = "/path/to/cachain.pem"


# The "real" hosts of clustered cas server that send SAML logout messages
# Assumes the cas server is load balanced across multiple hosts
#real_hosts[] = cas-real-1.example.com
#real_hosts[] = cas-real-2.example.com

# CAS client nodes for rebroadcasting pgtIou/pgtId and logoutRequest
#rebroadcast_nodes[] = "http://cas-client-1.example.com"
#rebroadcast_nodes[] = "http://cas-client-2.example.com"


