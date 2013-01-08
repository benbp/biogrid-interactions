CREATE OR REPLACE FUNCTION find_intermediates(VARCHAR, INT)
-- $1 is session id, $2 is filenum
RETURNS TABLE(
		sid VARCHAR, 
		intp VARCHAR) 
	AS $$
BEGIN
	IF EXISTS (SELECT 1 FROM interactors WHERE interactors.sid = $1 AND interactors.filenum = $2+1) THEN
		RETURN QUERY
		SELECT protein1.sid, protein1.intp FROM
			(SELECT interactors.sid, interactors.filenum, interactors.intp FROM interactors WHERE interactors.sid = $1 AND interactors.filenum = $2) AS protein1
			INNER JOIN 
				(SELECT * FROM find_intermediates($1, ($2+1) ) ) AS protein2
				ON protein1.intp = protein2.intp;
	ELSE
		RETURN QUERY SELECT interactors.sid, interactors.intp FROM interactors WHERE interactors.sid = $1 AND interactors.filenum = $2;
	END IF;
END; $$ LANGUAGE 'plpgSQL';