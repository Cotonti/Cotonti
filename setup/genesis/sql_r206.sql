/* r206 Hardened auth system */
ALTER TABLE sed_users ADD user_hashsalt CHAR(16) NOT NULL DEFAULT '';