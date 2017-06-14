PHP_ARG_ENABLE(ben,
        [Whether to enable the "ben" extension],
            [  enable-ben       Enable "ben" extension support])

  if test $PHP_BEN != "no"; then
    PHP_SUBST(BEN_SHARED_LIBADD)
  PHP_NEW_EXTENSION(ben, ben.c, $ext_shared)
    fi
