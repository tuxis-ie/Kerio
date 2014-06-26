#!/usr/bin/env python

from kerio import api

a = api.KerioApi(hostname = 'hostname', password = 'password', username = 'username', webmail = True)

params = {}
params['folderIds'] = []
params['query'] = {}
params['query']['start'] = 0
params['query']['limit'] = 500
print a.request('Contacts.get', params)

print a
