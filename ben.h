/*
 * =====================================================================================
 *
 *       Filename:  Ben.h
 *
 *    Description:  
 *
 *        Version:  1.0
 *        Created:  2017/06/12 21时50分43秒
 *       Revision:  none
 *       Compiler:  gcc
 *
 *         Author:  bugbear
 *   Organization:  
 *
 * =====================================================================================
 */


#ifndef Ben_H
#define Ben_H
#endif

//加载config.h，如果配置了的话
#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

extern zend_module_entry Ben_module_entry;
#define phpext_Ben_ptr &Ben_module_entry
PHP_METHOD(Ben, __construct);                                      
PHP_METHOD(Ben, __call);
PHP_METHOD(Ben, __callStatic);
PHP_METHOD(Ben, run);
PHP_MINIT_FUNCTION(ben);
