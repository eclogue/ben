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
#include "php_ini.h"
#include "ben.h"
#include "zend_extensions.h"
#include "zend_exceptions.h"
#include "ext/standard/info.h"

#if PHP_VERSION_ID >= 70000
#include "zend_smart_str.h"
#else
#include "ext/standard/php_smart_str.h"
#endif

/*#define php_ext_ben &ben_module_entry*/

zend_class_entry * ben_ce;
zend_class_entry * ben_error_ce;


ZEND_BEGIN_ARG_INFO_EX(ben_construct_arginfo, 0, 0, 2)
  ZEND_ARG_INFO(0, input)
  ZEND_ARG_INFO(0, output)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(ben_run_arginfo, 0, 0, 0)
ZEND_END_ARG_INFO()

PHP_INI_BEGIN()
   PHP_INI_ENTRY("ben.tmp",      "/tmp", PHP_INI_ALL, NULL)
PHP_INI_END()


const zend_function_entry ben_methods[] = {
  ZEND_ME(Ben, __construct, ben_construct_arginfo, ZEND_ACC_PUBLIC | ZEND_ACC_CTOR)
  ZEND_ME(Ben, run, ben_run_arginfo, ZEND_ACC_PUBLIC)
  {NULL, NULL, NULL}
};

zend_module_entry ben_module_entry = {
  STANDARD_MODULE_HEADER,
  "Ben",
  ben_methods,
  PHP_MINIT(ben), /* PHP_MINIT_FUNCTION */
  PHP_MSHUTDOWN(ben), /* MSHUTDOWN */
  PHP_RINIT(ben), /* RINIT */
  PHP_RSHUTDOWN(ben), /* RSHUTDOWN */
  PHP_MINFO(ben), /* MINFO */
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
  zval *name;
  php_printf("ben run name :%s \n", *name);
}

PHP_MINIT_FUNCTION(ben) {
  zend_class_entry ben;

  REGISTER_INI_ENTRIES();

  INIT_CLASS_ENTRY(ben, "Ben", ben_methods);
  #if PHP_VERSION_ID >= 70000
    ben_ce = zend_register_internal_class_ex(&ben, NULL);
  #else
    ben_ce = zend_register_internal_class_ex(&ben, NULL, NULL TSRMLS_CC);
  #endif
  return SUCCESS;
}
PHP_MSHUTDOWN_FUNCTION(ben)
{
	UNREGISTER_INI_ENTRIES();
	return SUCCESS;
}

PHP_RINIT_FUNCTION(ben)
{
	return SUCCESS;
}

PHP_RSHUTDOWN_FUNCTION(ben)
{
	return SUCCESS;
}

PHP_MINFO_FUNCTION(ben)
{
	php_info_print_table_start();
	php_info_print_table_header(2, "Version", "0.0.1");
	php_info_print_table_header(2, "Author", "ben");
	php_info_print_table_end();
}

#ifdef COMPILE_DL_BEN
ZEND_GET_MODULE(ben)
#endif
