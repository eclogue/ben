/*
 * =====================================================================================
 *
 *       Filename:  ben.c
 *
 *    Description:  ben
 *
 *        Version:  1.0
 *        Created:  2017/06/12 18时47分56秒
 *       Revision:  none
 *       Compiler:  gcc
 *
 *         Author:  bugbear 
 *   Organization: racecourse
 *
 * =====================================================================================
 */

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "ben.h"

/*#define php_ext_ben &ben_module_entry*/

zend_class_entry * ben_ce;
zend_class_entry * ben_error_ce;


ZEND_BEGIN_ARG_INFO_EX(ben_construct_arginfo, 0, 0, 2)
  ZEND_ARG_INFO(0, input)
  ZEND_ARG_INFO(0, output)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(ben_run_arginfo, 0, 0, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(ben_call_arginfo, 0, 0, 0)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(ben_call_static_arginfo, 0, 0, 0)
ZEND_END_ARG_INFO()

const zend_function_entry ben_methods[] = {
  ZEND_ME(Ben, __construct, ben_construct_arginfo, ZEND_ACC_PUBLIC | ZEND_ACC_CTOR)
  ZEND_ME(Ben, run, ben_run_arginfo, ZEND_ACC_PUBLIC)
  ZEND_ME(Ben, __call, ben_call_arginfo, ZEND_ACC_PUBLIC)
  ZEND_ME(Ben, __callStatic, ben_call_static_arginfo, ZEND_ACC_PUBLIC | ZEND_ACC_STATIC)
  {NULL, NULL, NULL}
};

zend_module_entry ben_module_entry = {
  STANDARD_MODULE_HEADER,
  "ben",
  ben_methods,
  PHP_MINIT(ben), // PHP_MINIT_FUNCTION
  NULL, /* MSHUTDOWN */
  NULL, /* RINIT */
  NULL, /* RSHUTDOWN */
  NULL, /* MINFO */
#if ZEND_MODULE_API_NO >= 20010901
  "2.1", //这个地方是我们扩展的版本
#endif
  STANDARD_MODULE_PROPERTIES 
};

ZEND_METHOD(Ben, __construct) {
  zval *input;
  zval *output;
  php_printf("ben __construct \n");
  RETURN_TRUE
}

ZEND_METHOD(Ben, run) {
  php_printf("ben run \n");
}

ZEND_METHOD(Ben, __call) {
  php_printf("ben call some thing \n");
}

ZEND_METHOD(Ben, __callStatic) {
  php_printf("ben static call \n");
}


#ifdef COMPILE_DL_WALU
ZEND_GET_MODULE(ben)
#endif
