#!/usr/bin/env python

from distutils.core import setup

setup(
    name='python-kerio-api',
    version='1.0',
    description='Python bindings to talk to the Kerio API',
    author='Mark Schouten',
    author_email='mark@tuxis.nl',
    url='https://github.com/tuxis-ie/Kerio/tree/master/kerioapi/',
    license='GPL',
    py_modules=['kerio.api'],
    package_dir={'': 'python'},
    platforms=['linux'],
    data_files=[
    ]
)

