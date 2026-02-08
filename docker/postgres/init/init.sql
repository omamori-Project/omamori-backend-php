-- 1) users
create table users (
  id bigserial primary key,
  email varchar(255) not null unique,
  password_hash varchar(255) not null,
  name varchar(100) not null,
  role varchar(20) not null default 'user',
  -- user | admin
  is_active boolean not null default true,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now(),
  deleted_at timestamptz
);
create index idx_users_deleted_at on users(deleted_at);
-- 2) files
create table files (
  id bigserial primary key,
  user_id bigint not null references users(id),
  purpose varchar(30) not null,
  -- omamori_element | render_output | frame_asset
  visibility varchar(10) not null default 'public',
  -- public | private
  file_key text not null unique,
  url text not null,
  content_type varchar(100),
  size_bytes bigint,
  width int,
  height int,
  created_at timestamptz not null default now(),
  deleted_at timestamptz
);
create index idx_files_user on files(user_id, created_at desc);
create index idx_files_deleted_at on files(deleted_at);
-- 3) fortune_colors
create table fortune_colors (
  id bigserial primary key,
  code varchar(60) not null unique,
  name varchar(60) not null,
  hex varchar(7) not null,
  category varchar(30),
  short_meaning varchar(120),
  meaning text,
  tips jsonb not null default '[]'::jsonb,
  is_active boolean not null default true,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now()
);
create index idx_fortune_colors_active on fortune_colors(is_active);
create index idx_fortune_colors_category on fortune_colors(category);
-- 4) frames
create table frames (
  id bigserial primary key,
  name varchar(80) not null,
  frame_key varchar(60) not null unique,
  preview_url text,
  asset_file_id bigint references files(id),
  is_active boolean not null default true,
  meta jsonb not null default '{}'::jsonb,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now(),
  deleted_at timestamptz
);
create index idx_frames_active on frames(is_active);
create index idx_frames_deleted_at on frames(deleted_at);
-- 5) omamoris
create table omamoris (
  id bigserial primary key,
  user_id bigint not null references users(id),
  title varchar(120) not null,
  meaning text,
  status varchar(20) not null default 'draft',
  -- draft | published
  theme varchar(30),
  size_code varchar(10),
  back_message text,
  applied_fortune_color_id bigint references fortune_colors(id),
  applied_frame_id bigint references frames(id),
  preview_file_id bigint references files(id),
  published_at timestamptz,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now(),
  deleted_at timestamptz
);
create index idx_omamoris_user on omamoris(user_id);
create index idx_omamoris_user_status on omamoris(user_id, status);
create index idx_omamoris_updated_at on omamoris(updated_at desc);
create index idx_omamoris_deleted_at on omamoris(deleted_at);
-- 6) omamori_elements
create table omamori_elements (
  id bigserial primary key,
  omamori_id bigint not null references omamoris(id) on delete cascade,
  type varchar(20) not null,
  -- text | stamp | image | background
  layer int not null default 0,
  props jsonb not null default '{}'::jsonb,
  transform jsonb not null default '{}'::jsonb,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now(),
  deleted_at timestamptz
);
create index idx_elements_omamori on omamori_elements(omamori_id);
create index idx_elements_omamori_layer on omamori_elements(omamori_id, layer);
create index idx_elements_deleted_at on omamori_elements(deleted_at);
create index idx_elements_props_gin on omamori_elements using gin(props);
-- 7) shares
create table shares (
  id bigserial primary key,
  omamori_id bigint not null references omamoris(id) on delete cascade,
  share_code varchar(32) not null unique,
  is_public boolean not null default true,
  expires_at timestamptz,
  created_at timestamptz not null default now(),
  revoked_at timestamptz
);
create index idx_shares_omamori on shares(omamori_id);
create index idx_shares_expires on shares(expires_at);
-- 8) posts
create table posts (
  id bigserial primary key,
  user_id bigint not null references users(id),
  omamori_id bigint references omamoris(id),
  title varchar(150) not null,
  content text not null,
  like_count int not null default 0,
  comment_count int not null default 0,
  bookmark_count int not null default 0,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now(),
  deleted_at timestamptz
);
create index idx_posts_created_at on posts(created_at desc);
create index idx_posts_user on posts(user_id, created_at desc);
create index idx_posts_deleted_at on posts(deleted_at);
-- 9) comments
create table comments (
  id bigserial primary key,
  post_id bigint not null references posts(id) on delete cascade,
  user_id bigint not null references users(id),
  parent_id bigint references comments(id),
  content text not null,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now(),
  deleted_at timestamptz
);
create index idx_comments_post on comments(post_id, created_at asc);
create index idx_comments_user on comments(user_id, created_at desc);
create index idx_comments_parent on comments(parent_id);
create index idx_comments_deleted_at on comments(deleted_at);
-- 10) post_likes
create table post_likes (
  post_id bigint not null references posts(id) on delete cascade,
  user_id bigint not null references users(id) on delete cascade,
  created_at timestamptz not null default now(),
  primary key (post_id, user_id)
);
create index idx_post_likes_user on post_likes(user_id, created_at desc);
-- 11) post_bookmarks
create table post_bookmarks (
  post_id bigint not null references posts(id) on delete cascade,
  user_id bigint not null references users(id) on delete cascade,
  created_at timestamptz not null default now(),
  primary key (post_id, user_id)
);
create index idx_post_bookmarks_user on post_bookmarks(user_id, created_at desc);
-- 12) renders
create table renders (
  id bigserial primary key,
  render_code varchar(40) not null unique,
  user_id bigint not null references users(id),
  omamori_id bigint references omamoris(id),
  side varchar(10) not null default 'front',
  -- front | back | both
  format varchar(10) not null default 'png',
  dpi int not null default 150,
  width int,
  height int,
  store varchar(10) not null default 'temp',
  -- temp | persist
  file_id bigint references files(id),
  expires_at timestamptz,
  created_at timestamptz not null default now()
);
create index idx_renders_user on renders(user_id, created_at desc);
create index idx_renders_omamori on renders(omamori_id, created_at desc);
create index idx_renders_expires on renders(expires_at);