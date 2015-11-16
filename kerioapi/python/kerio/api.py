#!/usr/bin/env python

import sys
import requests
import simplejson as json

class KerioApi():
    def __init__(self, hostname = None, username = None, password = None, nossl = False, verifyssl = True):
        if type(self) == KerioApi:
            raise Exception("<KerioApi> must be subclassed by Kerio(Connect|Operator|Control)Api.")

        self.hostname = hostname
        self.username = username
        self.password = password
        self.verifyssl = verifyssl

        self.requestid = 0
        self.token     = ''
        self.cookie    = ''
        self.wclient   = ''

    def port(self, setport):
        if setport == 80:
            self.proto = 'http'
        else:
            self.proto = 'https'

        self.urlport     = str(setport)

    def construct_uri(self):
        if self.hostname == '':
            self.error('Hostname not set')

        if self.username == '':
            self.error('Username not set')

        if self.password == '':
            self.error('Password not set')

        self.uri = self.proto+'://'+self.hostname+':'+self.urlport+self.url

    def rheaders(self):
        headers = {}
        headers['Content-Type'] = 'application/json'

        if hasattr(self, 'token'):
            headers['X-Token'] = self.token
        if hasattr(self, 'cookie'):
            headers['Cookie'] = self.cookie

        return headers

    def rparams(self):
        self.setrequestid()
        params = {}
        params['jsonrpc'] = '2.0'
        params['id'] = self.requestid
        params['params'] = {}

        return params

    def setrequestid(self):
        self.requestid = self.requestid + 1

    def uploadfile(self, imagedata, filename):
        self.construct_uri()
        headers = self.rheaders()
        params  = self.rparams()
        headers['Content-Description'] = str(filename).split('/')[-1]
        headers['Content-Length']      = len(imagedata)
        headers['Content-Type']        = 'image/jpeg'
        
        uri = self.uri
        if self.dowebmail == True:
            uri = uri+'attachment-upload/'

        r = requests.post(uri, data=imagedata, headers=headers, verify=self.verifyssl)
        return self.handlerequest(r)
        
    def request(self, method, sendparams = {}):
        self.construct_uri()
        headers = self.rheaders()
        params  = self.rparams()

        if self.token == '':
            params['method'] = 'Session.login'
            params['params']['application'] = {}
            params['params']['application']['name'] = 'PythonApi'
            params['params']['application']['vendor'] = 'Tuxis'
            params['params']['application']['version'] = '1.1'
            params['params']['userName'] = self.username
            params['params']['password'] = self.password
            r = requests.post(self.uri, data=json.dumps(params), headers=headers, verify=self.verifyssl)
            result = json.loads(r.text)
            if result.has_key('error'):
                self.error(result['error'])
            if result.has_key('result'):
                if result['result'].has_key('token'):
                    self.token = result['result']['token']
                    self.cookie = str(r.headers['set-cookie']).split(' ')[0]
            self.requestid = self.requestid + 1

        if self.token == '':
            self.error('Looks like logging in failed')

        params  = self.rparams()
        headers = self.rheaders()
        params['method'] = method
        params['params'] = sendparams
        if len(params['params']) == 0:
            del params['params']

        r = requests.post(self.uri, data=json.dumps(params), headers=headers, verify=self.verifyssl)
        return self.handlerequest(r)

    def handlerequest(self, r):
        result = json.loads(r.text)

        if result.has_key('error'):
            self.error(result['error'])
        if result.has_key('errors'):
            self.error(result['errors'][0]['message'])
        if result.has_key('result'):
            return result['result']
     

    def error(self, message, method = ''):
        m = 'No specific method: '

        if method != '':
            m = "While running %s" % method
        
        raise SystemError("%s %s" % (m, message))


class KerioConnectApi(KerioApi):
    def __init__(self, hostname = None, username = None, password = None, client = False, nossl = False, verifyssl = True):
        KerioApi.__init__(self, hostname = hostname, username = username, password = password, nossl = nossl, verifyssl = verifyssl)

        self.url  = '/admin/api/jsonrpc/'
        self.port(4040)

        if client:
            self.url = '/webmail/api/jsonrpc/'
            self.port(443)
            if nossl:
                self.port(80)

class KerioOperatorApi(KerioApi):
    def __init__(self, hostname = None, username = None, password = None, client = False, nossl = False, verifyssl = True):
        KerioApi.__init__(self, hostname = hostname, username = username, password = password, nossl = nossl, verifyssl = verifyssl)

        self.url  = '/admin/api/jsonrpc/'
        self.port(4021)

        if client:
            self.url = '/myphone/api/jsonrpc/'
            self.port(443)
            if nossl:
                self.port(80)

class KerioControlApi(KerioApi):
    def __init__(self, hostname = None, username = None, password = None, nossl = False, verifyssl = True):
        KerioApi.__init__(self, hostname = hostname, username = username, password = password, nossl = nossl, verifyssl = verifyssl)

        self.url  = '/admin/api/jsonrpc/'
        self.port(4081)
