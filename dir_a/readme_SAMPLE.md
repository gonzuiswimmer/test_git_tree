# 手順メモ

## 初期設定

### 1. clone subProject

- 任意のディレクトリで git clone ${サブプロジェクト}

### 2. change branch

- cd ./${サブプロジェクト}
- git branch --contains
- git checkout main ?

### 3. コピー .git ファイル

- 任意のディレクトリの.git ファイルを、メインプロジェクトのサブプロジェクトディレクトリにコピー
- git 拡張機能の「ソース管理」>「表示と並び替え」>「リポジトリ」で、サブプロジェクトの git 管理チェックを外しておく

## staging するとき

### 1. PR 作成

- メインプロジェクトで、main(develop)→staging ブランチへの PR を作成＆merge

### 2. staging に移動して同期

- staging ブランチに移動してプル git checkout staging && git pull origin staging

### 3. サブディレクトリ内でブランチと PR を作成

- cd ./${サブディレクトリ}
- git remote -v (サブプロジェクトに繋がっていることを確認)
- git checkout staging && git pull origin staging (サブプロジェクトの main ブランチを pull して最新であることを確認)
- git checkout -b feature/yyyymmdd
- git 拡張機能の「ソース管理」>「表示と並び替え」>「リポジトリ」で、サブプロジェクトの git 管理チェックをつけ、差分を add & commit & push
