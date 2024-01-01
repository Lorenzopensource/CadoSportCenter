<?php

	namespace CSC;

	class CSCAccess {
		private const HOST_DB = "localhost";
		private const DATABASE_NAME = "csc_db";
		private const USERNAME = "root";
		private const PASSWORD = "";

		private $connection;
		
		/** INSTAURAZIONE DELLA CONNESSIONE */
		public function openConnection() {
            try {
                $this->connection = mysqli_connect(
                    self::HOST_DB,
                    self::USERNAME,
                    self::PASSWORD,
                    self::DATABASE_NAME
                );
    
                if (!$this->connection) {
                    throw new \mysqli_sql_exception("Failed to connect to MySQL: " . mysqli_connect_error());
                }
                return true;
            } catch (\mysqli_sql_exception $e) {
                echo "Connection failed: " . $e->getMessage();
                return false;
            }
        }

        /** CHIUSURA DELLA CONNESSIONE */
        public function closeConnection() {
            try {
                if ($this->connection) {
                    mysqli_close($this->connection);
                    $this->connection = null;
                    return true;
                } else {
                    throw new \Exception("Connection is not established.");
                }
            } catch (\Exception $e) {
                echo "Error while closing connection: " . $e->getMessage();
                return false;
            }
        }
        
        /** VERIFICA CORRETTEZZA DELLE CREDENZIALI-LOGIN  DEL CLIENTE */
        public function checkLoginClientCredentials($email, $password) {
            $queryCheck = "SELECT * FROM Cliente WHERE Email=\"$email\" AND Pass_hash=\"$password\"";

            $queryResult = mysqli_query($this->connection, $queryCheck) or die("Errore in DBAccess" . mysqli_error($this->connection));
            
			return mysqli_num_rows($queryResult) > 0;
        }
        
        /** VERIFICA SE L'UTENTE HA GIA EFFETTUATO RICHIESTE/PRENOTAZIONI 
         *              (se l'email già presente nel db) */
        public function checkUser($email) {
            $queryCheck = "SELECT * FROM Utente WHERE Email=\"$email\"";

            $queryResult = mysqli_query($this->connection, $queryCheck) or die("Errore in DBAccess" . mysqli_error($this -> connection));

			return mysqli_num_rows($queryResult) > 0;
        }

		/** VERIFICA SE L'UTENTE è GIA REGISTRATO */
        public function checkRegisteredClient($email) {
            $queryCheck = "SELECT * FROM Cliente  WHERE Email=\"$email\"";

            $queryResult = mysqli_query($this->connection, $queryCheck) or die("Errore in DBAccess" . mysqli_error($this -> connection));

			return mysqli_num_rows($queryResult) > 0;
        }

        /** INSERIMENTO NUOVO UTENTE */
        public function insertNewUser($email, $nome) {
			/* Inserimento in db con valori "" (campo vuoto) se il valori sono null sui campi che accettano null */
			$queryInsert = "INSERT INTO Utente(Email, Nome) 
								VALUES (\"$email\", \"$nome\")";
			
			mysqli_query($this->connection, $queryInsert) or die(mysqli_error($this->connection));

			return mysqli_affected_rows($this->connection) > 0;
		}

        /** INSERIMENTO NUOVO CLIENTE */
        public function insertNewClient($email, $nome, $cognome, $password) {
			/* Inserimento in db con valori "" (campo vuoto) se il valori sono null sui campi che accettano null */
			$queryInsert = "INSERT INTO Cliente(Email, Nome, Cognome, Pass_hash) 
								VALUES (\"$email\", \"$nome\", \"$cognome\", \"$password\")";
			
			mysqli_query($this->connection, $queryInsert) or die(mysqli_error($this->connection));

			return mysqli_affected_rows($this->connection) > 0;
		}

        /** INSERIMENTO NUOVA PRENOTAZIONE */
        public function insertNewPrenotation($campo, $attività, $utente, $data, $ora) {
			/* Inserimento in db con valori "" (campo vuoto) se il valori sono null sui campi che accettano null */
			$queryInsert = "INSERT INTO Prenotazione(Codice_campo, Id_Attivita, Utente, Data, Ora) 
								VALUES (\"$campo\", \"$attività\", \"$utente\", \"$data\", \"$ora\")";
			
			mysqli_query($this->connection, $queryInsert) or die(mysqli_error($this->connection));

			return mysqli_affected_rows($this->connection) > 0;
		}

        /** INSERIMENTO NUOVA RICHIESTA */
        public function insertNewRequest($email, $titolo, $testo) {
			/* Inserimento in db con valori "" (campo vuoto) se il valori sono null sui campi che accettano null */
			$queryInsert = "INSERT INTO Richieste(Email, Titolo, Testo) 
								VALUES (\"$email\", \"$titolo\", \"$testo\")";
			
			mysqli_query($this->connection, $queryInsert) or die(mysqli_error($this->connection));

			return mysqli_affected_rows($this->connection) > 0;
		}

        /** RIMOZIONE PRENOTAZIONE */
        public function removePrenotation($id) {
            $queryDelete = "DELETE FROM Prenotazione WHERE id=\"$id\"";
            
            mysqli_query($this->connection, $queryDelete) or die(mysqli_error($this->connection));
            
            return mysqli_affected_rows($this->connection) > 0;
        }
        
        /** OTTENIMENTO DI TUTTE LE PRENOTAZIONI DI UN UNTENTE */
        public function getTClientPrenotations($email) {
			$query = "SELECT * FROM Prenotazione WHERE Utente=\"$email\"";
			
			$queryResult = mysqli_query($this->connection, $query) or die("Errore in DBAccess" . mysqli_error($this -> connection));

			if(mysqli_num_rows($queryResult) != 0){
				$result = array();
				while($row = mysqli_fetch_assoc($queryResult)) 
				{
					$result[] = $row;
				}
				$queryResult -> free;
				return $result;
			} 
			else {
				return null;
			}
		}

        /** OTTENIMENTO DI TUTTE LE PRENOTAZIONI DI TUTTI GLI UTENTI */
        public function getAllPrenotations() {
			$query = "SELECT * FROM Prenotazione";// ...
			
			$queryResult = mysqli_query($this->connection, $query) or die("Errore in DBAccess" . mysqli_error($this -> connection));

			if(mysqli_num_rows($queryResult) != 0){
				$result = array();
				while($row = mysqli_fetch_assoc($queryResult)) 
				{
					$result[] = $row;
				}
				$queryResult -> free;
				return $result;
			} 
			else {
				return null;
			}
		}

		/** OTTENIMENTO DI TUTTE LE RICHIESTE */
		public function getAllRequests() {
			$query = "SELECT * FROM Richieste";// ...
			
			$queryResult = mysqli_query($this->connection, $query) or die("Errore in DBAccess" . mysqli_error($this -> connection));

			if(mysqli_num_rows($queryResult) != 0){
				$result = array();
				while($row = mysqli_fetch_assoc($queryResult)) 
				{
					$result[] = $row;
				}
				$queryResult -> free;
				return $result;
			} 
			else {
				return null;
			}
		}

		/** OTTENIMENTO DEGLI ORARI DELLE PRENOTAZIONI EFFETTUATE IN UNA CERTA DATA
		 * 	(utile per visualizzare in seguito le disponibilità restanti) */
		public function getReservedPrenotations($data_scelta) {
			$query = "SELECT ora FROM Prenotazione WHERE data=\"$data_scelta\"";// ...
			
			$queryResult = mysqli_query($this->connection, $query) or die("Errore in DBAccess" . mysqli_error($this -> connection));

			if(mysqli_num_rows($queryResult) != 0){
				$result = array();
				while($row = mysqli_fetch_assoc($queryResult)) 
				{
					$result[] = $row;
				}
				$queryResult -> free;
				return $result;
			} 
			else {
				return null;
			}
		}
	}
?>